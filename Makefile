#!/usr/bin/make

SHELL = /bin/bash
REGISTRY_HOST=dockerhub.hello-print.ru
REGISTRY_PATH=generic-images/

VAULT_ADDR=https://vault.hello-print.ru/v1/mservis/data/erp-docker/
VAULT_TOKEN=s.hVTX8k96eRxjHTfJAhrnAvJ9

PULL_TAG=latest
TAG=dev
NGINX_PORT=80
COMPOSE_HTTP_TIMEOUT=600
SET=no
CONFIG=makefile.config
-include ${CONFIG}

APP_IMAGE=$(REGISTRY_HOST)/$(REGISTRY_PATH)nginx-phpfpm-docker
DB_IMAGE=$(REGISTRY_HOST)/$(REGISTRY_PATH)mariadb-mservis-docker
REDIS_IMAGE=$(REGISTRY_HOST)/$(REGISTRY_PATH)redis
SCHEMASPY_IMAGE=$(REGISTRY_HOST)/$(REGISTRY_PATH)schemaspy

docker_bin := $(shell command -v docker 2> /dev/null)
docker_compose_bin := $(shell command -v docker-compose 2> /dev/null)

all_images = $(APP_IMAGE):$(PULL_TAG) \
             $(DB_IMAGE):$(TAG) \
             $(REDIS_IMAGE):$(PULL_TAG) \
             $(SCHEMASPY_IMAGE):$(PULL_TAG)

.DEFAULT_GOAL := help

# This will output the help for each task.

help: ## Show this help
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "  \033[36m%-15s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST)
	@echo -e "\n Allowed for overriding next properties: \n \
	\t TAG - Used for: clean, db-pull, build, shell, watch and all get commands \n \
	\t      (dev by default) \n \
	\t NGINX_PORT - Frontend port \n \
	\t      (80 by default) \n \
	You can override variable in Makefile or use SET in yes values. \n \
	Usage example if you want to set variable manual in command line: \n \
	\t \033[33mmake SET=yes init \033[37m \n"

install: ## Install requirement apps (use with sudo)
	@apt update && \
	apt install curl jq -y

# --- [ Docker ] -------------------------------------------------------------------------------------------------

login: ## Log in to a remote Docker registry
	@if [ -d "$$HOME/.docker" ]; then \
		cp docker-config.json $$HOME/.docker/config.json && \
		chmod 600 $$HOME/.docker/config.json; \
	else \
		mkdir $$HOME/.docker && \
		chmod 700 $$HOME/.docker && \
		cp docker-config.json $$HOME/.docker/config.json && \
		chmod 600 $$HOME/.docker/config.json; \
	fi
	@$(docker_bin) login $(REGISTRY_HOST)

clean: set-env ## Remove images from local registry, local volumes and created dirs
	@$(docker_compose_bin) down -v
	@$(foreach image,$(all_images),$(docker_bin) rmi -f $(image);)
	@$(docker_bin) volume prune -f
	@rm -rf ./volumes/dbschema/* storage_server storage/logs storage/tmp storage/framework/cache storage/framework/sessions storage/framework/views
	@echo -e "\033[32mSuccessfully deleted: \033[37m * images"
	@echo -e "\t\t       * volumes"
	@echo -e "\t\t       * directories"

app-pull: ## Application - pull latest Docker image (from remote registry)
	@$(docker_bin) pull "$(APP_IMAGE):$(PULL_TAG)"

redis-pull: ## Redis - pull latest Docker image (from remote registry)
	@$(docker_bin) pull "$(REDIS_IMAGE):$(PULL_TAG)"

db-pull: set-env ## Database - pull image with required env (from remote registry)
	@$(docker_bin) pull "$(DB_IMAGE):$(TAG)"

set-env: ## Setting nginx port and required environment
ifeq (yes, $(SET))
	$(eval TAG := $(shell read -p "Please set environment (dev/stage/template): " enter; echo $${enter:-$(TAG)}))
	$(eval COMPOSE_HTTP_TIMEOUT := $(shell read -p "Please set COMPOSE_HTTP_TIMEOUT (default 600s): " enter1; echo $${enter1:-$(COMPOSE_HTTP_TIMEOUT)}))
	$(eval NGINX_PORT := $(shell read -p "Please set nginx port (default 80): " enter2; echo $${enter2:-$(NGINX_PORT)})) @echo -e "\033[32mSuccessfully settings:\033[37m    Environment: \"$(TAG)\" \n\t\t\t  Nginx port:  \"$(NGINX_PORT)\" \n\t\t\t  COMPOSE_HTTP_TIMEOUT: \"$(COMPOSE_HTTP_TIMEOUT)\"";
	@sed -e "s/{TAG}/$(TAG)/g ; s/{COMPOSE_HTTP_TIMEOUT}/$(COMPOSE_HTTP_TIMEOUT)/g ; s/{NGINX_PORT}/$(NGINX_PORT)/g" makefile-config.template > makefile.config
else
	@echo -e "\033[32mCurrent settings:\033[37m    Environment: \"$(TAG)\" \n\t\t     Nginx port:  \"$(NGINX_PORT)\" \n\t\t     COMPOSE_HTTP_TIMEOUT:  \"$(COMPOSE_HTTP_TIMEOUT)\"";
endif

get-env: set-env ## Getting variables for the required environment
	$(eval MARIADB_DATABASE := $(shell curl -s --header "X-Vault-Token: $(VAULT_TOKEN)" $(VAULT_ADDR)$(TAG) | jq -r .data.data.MARIADB_DATABASE))
	$(eval MARIADB_USER := $(shell curl -s --header "X-Vault-Token: $(VAULT_TOKEN)" $(VAULT_ADDR)$(TAG) | jq -r .data.data.MARIADB_USER))
	$(eval MARIADB_PASSWORD := $(shell curl -s --header "X-Vault-Token: $(VAULT_TOKEN)" $(VAULT_ADDR)$(TAG) | jq -r .data.data.MARIADB_PASSWORD))
	@sed -e "s/{TAG}/$(TAG)/g ; s/{MARIADB_DATABASE}/$(MARIADB_DATABASE)/g ; s/{MARIADB_USER}/$(MARIADB_USER)/g ; s/{MARIADB_PASSWORD}/$(MARIADB_PASSWORD)/g" .env.template > .env
	@echo -e "\033[32mSuccessfully created: \033[37m   .env"

get-compose: set-env  ## Creating docker-compose for the required environment and nginx port
	@sed -e "s/{TAG}/$(TAG)/g ; s/{PORT}/$(NGINX_PORT)/g" docker-compose.template > docker-compose.yaml
	@echo -e "\033[32mSuccessfully created: \033[37m   docker-compose.yaml"

get-nginx: set-env ## Creating nginx config file for the required environment and nginx port
	@sed -e "s/{TAG}/$(TAG)/g ; s/{PORT}/$(NGINX_PORT)/g" volumes/etc/nginx/sites-enabled/default > volumes/etc/nginx/sites-enabled/default.conf
	@echo -e "\033[32mSuccessfully created: \033[37m   volumes/etc/nginx/sites-enabled/default.conf"

get-dbschema: set-env  ## Creating dbschema.conf for the required environment
	$(eval DB_DATABASE := $(shell curl -s --header "X-Vault-Token: $(VAULT_TOKEN)" $(VAULT_ADDR)$(TAG) | jq -r .data.data.MARIADB_DATABASE))
	$(eval DB_USER := $(shell curl -s --header "X-Vault-Token: $(VAULT_TOKEN)" $(VAULT_ADDR)$(TAG) | jq -r .data.data.MARIADB_USER))
	$(eval DB_PASSWORD := $(shell curl -s --header "X-Vault-Token: $(VAULT_TOKEN)" $(VAULT_ADDR)$(TAG) | jq -r .data.data.MARIADB_PASSWORD))
	@sed -e "s/{TAG}/$(TAG)/g ; s/{DB_DATABASE}/$(DB_DATABASE)/g ; s/{DB_USER}/$(DB_USER)/g ; s/{DB_PASSWORD}/$(DB_PASSWORD)/g" volumes/dbschema.template > volumes/dbschema.conf
	@echo -e "\033[32mSuccessfully created: \033[37m   volumes/dbschema.conf"

# --- [ Docker-compose ] ----------------------------------------------------------------------------------------

---------------: ## -------------------------------------------------------------------------------------

up: set-env ## Start all containers (in background) and create required dirs
	@COMPOSE_HTTP_TIMEOUT=$(COMPOSE_HTTP_TIMEOUT) $(docker_compose_bin) up --no-recreate -d
	@echo "Check dirs..................................................................................................................."
	@$(docker_bin) exec -it erp_nginx_$(TAG) bash -c 'if [ -d /var/www/mservis.co/data/storage_server ] ; then \
		echo -e "Directory already exist /var/www/mservis.co/data/storage_server                                                    (\033[32m OK! \033[37m)"; \
	else \
		mkdir -p /var/www/mservis.co/data/storage_server && \
		echo -e "Directory created /var/www/mservis.co/data/storage_server                                                          (\033[32m OK! \033[37m)"; \
	fi'
	@$(docker_bin) exec -it erp_nginx_$(TAG) bash -c 'if [ -d /var/www/mservis.co/data/storage/logs ] ; then \
		echo -e "Directory already exist /var/www/mservis.co/data/storage/logs                                                      (\033[32m OK! \033[37m)"; \
	else \
		mkdir -p /var/www/mservis.co/data/storage/logs && \
		echo -e "Directory created /var/www/mservis.co/data/storage/logs                                                            (\033[32m OK! \033[37m)"; \
	fi'
	@$(docker_bin) exec -it erp_nginx_$(TAG) bash -c 'if [ -d /var/www/mservis.co/data/storage/tmp ] ; then \
		echo -e "Directory already exist /var/www/mservis.co/data/storage/tmp                                                       (\033[32m OK! \033[37m)"; \
	else \
		mkdir -p /var/www/mservis.co/data/storage/tmp && \
		echo -e "Directory created /var/www/mservis.co/data/storage/tmp                                                             (\033[32m OK! \033[37m)"; \
	fi'
	@$(docker_bin) exec -it erp_nginx_$(TAG) bash -c 'if [ -d /var/www/mservis.co/data/storage/framework/cache ] ; then \
		echo -e "Directory already exist /var/www/mservis.co/data/storage/framework/cache                                           (\033[32m OK! \033[37m)"; \
	else \
		mkdir -p /var/www/mservis.co/data/storage/framework/cache && \
		echo -e "Directory created /var/www/mservis.co/data/storage/framework/cache                                                 (\033[32m OK! \033[37m)"; \
	fi'
	@$(docker_bin) exec -it erp_nginx_$(TAG) bash -c 'if [ -d /var/www/mservis.co/data/storage/framework/sessions ] ; then \
		echo -e "Directory already exist /var/www/mservis.co/data/storage/framework/sessions                                        (\033[32m OK! \033[37m)"; \
	else \
		mkdir -p /var/www/mservis.co/data/storage/framework/sessions && \
		echo -e "Directory created /var/www/mservis.co/data/storage/framework/sessions                                              (\033[32m OK! \033[37m)"; \
	fi'
	@$(docker_bin) exec -it erp_nginx_$(TAG) bash -c 'if [ -d /var/www/mservis.co/data/storage/framework/views ] ; then \
		echo -e "Directory already exist /var/www/mservis.co/data/storage/framework/views                                           (\033[32m OK! \033[37m)"; \
	else \
		mkdir -p /var/www/mservis.co/data/storage/framework/views && \
		echo -e "Directory created /var/www/mservis.co/data/storage/framework/views                                                 (\033[32m OK! \033[37m)"; \
	fi'
	@echo "Check symlinks..............................................................................................................."
	@$(docker_bin) exec -it erp_nginx_$(TAG) bash -c 'if [ -e /var/www/mservis.co/data/public/storage_server ] ; then \
		echo -e "Symlink already exist /var/www/mservis.co/data/storage_server -> /var/www/mservis.co/data/public/storage_server (\033[32m OK! \033[37m)"; \
	else \
		ln -s /var/www/mservis.co/data/storage_server /var/www/mservis.co/data/public/storage_server && \
		echo -e "Symlink created /var/www/mservis.co/data/storage_server -> /var/www/mservis.co/data/public/storage_server       (\033[32m OK! \033[37m)"; \
	fi'
	@$(docker_bin) exec -it erp_nginx_$(TAG) bash -c 'if [ -e /var/www/mservis.co/data/public/storage ] ; then \
		echo -e "Symlink already exist /var/www/mservis.co/data/storage -> /var/www/mservis.co/data/public/storage               (\033[32m OK! \033[37m)"; \
	else \
		ln -s /var/www/mservis.co/data/storage_server /var/www/mservis.co/data/public/storage && \
		echo -e "Symlink created /var/www/mservis.co/data/storage_server -> /var/www/mservis.co/data/public/storage_server       (\033[32m OK! \033[37m)"; \
	fi'
	@$(docker_bin) exec -it erp_nginx_$(TAG) bash -c 'if [ -e /var/www/mservis.co/data/public/storage ] ; then \
		echo -e "Symlink already exist /var/www/mservis.co/data/storage/app/public -> /var/www/mservis.co/data/public/storage    (\033[32m OK! \033[37m)"; \
	else \
		ln -s /var/www/mservis.co/data/storage/app/public /var/www/mservis.co/data/public/storage && \
		echo -e "Symlink created /var/www/mservis.co/data/storage/app/public -> /var/www/mservis.co/data/public/storage          (\033[32m OK! \033[37m)"; \
	fi'
	@echo "Check hosts..................................................................................................................."
	@if cat /etc/hosts | grep "mservis.local" > /dev/null 2>&1 ; then \
		echo -e "Records already exist!                                                                                                (\033[31m FAIL!\033[37m)" && \
		echo "Delete old records" && \
		cat /etc/hosts | grep "mservis.local" | xargs -I '{}' sed -i "s/{}//g" /etc/hosts && \
		echo -e "Old records successefully deleted                                                                                     (\033[32m OK! \033[37m)" && \
		echo "Insert new record" && \
		$(docker_bin) inspect -f '{{range.NetworkSettings.Networks}}{{.IPAddress}}{{end}}' erp_nginx_$(TAG) | xargs -I {} echo "{} $(TAG).mservis.local dbschema-$(TAG).mservis.local" >> /etc/hosts && \
		$(docker_bin) inspect -f '{{range.NetworkSettings.Networks}}{{.IPAddress}}{{end}}' erp_mariadb_$(TAG) | xargs -I {} echo "{} db-$(TAG).mservis.local" >> /etc/hosts && \
		echo -e "New record successefully placed                                                                                       (\033[32m OK! \033[37m)" ; \
	else \
		echo "Old records does not exist, insert new record" && \
		$(docker_bin) inspect -f '{{range.NetworkSettings.Networks}}{{.IPAddress}}{{end}}' erp_nginx_$(TAG) | xargs -I {} echo "{} $(TAG).mservis.local dbschema-$(TAG).mservis.local" >> /etc/hosts && \
		$(docker_bin) inspect -f '{{range.NetworkSettings.Networks}}{{.IPAddress}}{{end}}' erp_mariadb_$(TAG) | xargs -I {} echo "{} db-$(TAG).mservis.local" >> /etc/hosts && \
		echo -e "New record successefully placed                                                                                       (\033[32m OK! \033[37m)" ; \
	fi
	@echo -e "Sites available on: * http://$(TAG).mservis.local:$(NGINX_PORT) \n                    * http://dbschema-$(TAG).mservis.local:$(NGINX_PORT) \nDB available on:    * db-$(TAG).mservis.local:3306"

down: ## Stop and remove all started for development containers
	@$(docker_compose_bin) down

stop: ## Stop all started for development containers
	@$(docker_compose_bin) stop

restart: ## Restart all started for development containers and add new records on /etc/hosts
	@COMPOSE_HTTP_TIMEOUT=$(COMPOSE_HTTP_TIMEOUT) $(docker_compose_bin) up -d --force-recreate
	@echo "Check hosts..................................................................................................................."
	@if cat /etc/hosts | grep "mservis.local" > /dev/null 2>&1 ; then \
		echo -e "Records already exist!                                                                                                (\033[31m FAIL!\033[37m)" && \
		echo "Delete old records" && \
		cat /etc/hosts | grep "mservis.local" | xargs -I '{}' sed -i "s/{}//g" /etc/hosts && \
		echo -e "Old records successefully deleted                                                                                     (\033[32m OK! \033[37m)" && \
		echo "Insert new record" && \
		$(docker_bin) inspect -f '{{range.NetworkSettings.Networks}}{{.IPAddress}}{{end}}' erp_nginx_$(TAG) | xargs -I {} echo "{} $(TAG).mservis.local dbschema-$(TAG).mservis.local" >> /etc/hosts && \
		$(docker_bin) inspect -f '{{range.NetworkSettings.Networks}}{{.IPAddress}}{{end}}' erp_mariadb_$(TAG) | xargs -I {} echo "{} db-$(TAG).mservis.local" >> /etc/hosts && \
		echo -e "New record successefully placed                                                                                       (\033[32m OK! \033[37m)" ; \
	else \
		echo "Old records does not exist, insert new record" && \
		$(docker_bin) inspect -f '{{range.NetworkSettings.Networks}}{{.IPAddress}}{{end}}' erp_nginx_$(TAG) | xargs -I {} echo "{} $(TAG).mservis.local dbschema-$(TAG).mservis.local" >> /etc/hosts && \
		$(docker_bin) inspect -f '{{range.NetworkSettings.Networks}}{{.IPAddress}}{{end}}' erp_mariadb_$(TAG) | xargs -I {} echo "{} db-$(TAG).mservis.local" >> /etc/hosts && \
		echo -e "New record successefully placed                                                                                       (\033[32m OK! \033[37m)" ; \
	fi
	@echo -e "Sites available on: * http://$(TAG).mservis.local:$(NGINX_PORT) \n                    * http://dbschema-$(TAG).mservis.local:$(NGINX_PORT) \nDB available on:    * db-$(TAG).mservis.local:3306"

build: set-env ## Build application (required running application)
	@$(docker_bin) exec -it erp_phpfpm_$(TAG) bash -c "cd /var/www/mservis.co/data/subdomain && yarn build"
	@$(docker_bin) exec -it erp_phpfpm_$(TAG) bash -c "cd /var/www/mservis.co/data && yarn build"

composer: set-env ## Run composer install (required running application)
	@$(docker_bin) exec -it erp_phpfpm_$(TAG) bash -c "cd /var/www/mservis.co/data && composer install"

shell: set-env ## Start shell into application container
	@$(docker_bin) exec -it erp_phpfpm_$(TAG) bash

watch: set-env ## Run yarn watch in site root dir
	@$(docker_bin) exec -it erp_phpfpm_$(TAG) bash -c "cd /var/www/mservis.co/data && yarn start"

hosts: set-env ## Check and replace sites ip-address in /etc/hosts (use with sudo)
	@echo "Check hosts..................................................................................................................."
	@if cat /etc/hosts | grep "mservis.local" > /dev/null 2>&1 ; then \
		echo -e "Records already exist!                                                                                                (\033[31m FAIL!\033[37m)" && \
		echo "Delete old records" && \
		cat /etc/hosts | grep "mservis.local" | xargs -I '{}' sed -i "s/{}//g" /etc/hosts && \
		echo -e "Old records successefully deleted                                                                                     (\033[32m OK! \033[37m)" && \
		echo "Insert new record" && \
		$(docker_bin) inspect -f '{{range.NetworkSettings.Networks}}{{.IPAddress}}{{end}}' erp_nginx_$(TAG) | xargs -I {} echo "{} $(TAG).mservis.local dbschema-$(TAG).mservis.local" >> /etc/hosts && \
		$(docker_bin) inspect -f '{{range.NetworkSettings.Networks}}{{.IPAddress}}{{end}}' erp_mariadb_$(TAG) | xargs -I {} echo "{} db-$(TAG).mservis.local" >> /etc/hosts && \
		echo -e "New record successefully placed                                                                                       (\033[32m OK! \033[37m)" ; \
	else \
		echo "Old records does not exist, insert new record" && \
		$(docker_bin) inspect -f '{{range.NetworkSettings.Networks}}{{.IPAddress}}{{end}}' erp_nginx_$(TAG) | xargs -I {} echo "{} $(TAG).mservis.local dbschema-$(TAG).mservis.local" >> /etc/hosts && \
		$(docker_bin) inspect -f '{{range.NetworkSettings.Networks}}{{.IPAddress}}{{end}}' erp_mariadb_$(TAG) | xargs -I {} echo "{} db-$(TAG).mservis.local" >> /etc/hosts && \
		echo -e "New record successefully placed                                                                                       (\033[32m OK! \033[37m)" ; \
	fi
	@echo -e "Sites available on: * http://$(TAG).mservis.local:$(NGINX_PORT) \n                    * http://dbschema-$(TAG).mservis.local:$(NGINX_PORT) \nDB available on:    * db-$(TAG).mservis.local:3306"

migrate: ## Run migrations
	@$(docker_bin) exec -it erp_phpfpm_$(TAG) bash -c "cd /var/www/mservis.co/data && php artisan migrate --seed --force"

init: install login get-env get-compose get-nginx get-dbschema ## Install required packages and generates files to run the application (use with sudo)
	@echo -e "\n\033[32mSuccessfully complete init \033[37m \nuse: * make up - to run the application \n     * make build - to build the application (\033[31m after make up !!!\033[37m )"
