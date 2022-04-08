<?php

namespace App\Services\Processing\Processors;

/**
 * Processors factory.
 */
class ProcessorFactory
{
    /**
     * Return processor based on a given ID of a processing operator.
     *
     * @param  int    $processingOperatorId
     *
     * @return ProcessorInterface|null
     */
    public static function create(int $processingOperatorId): ?ProcessorInterface
    {
        switch ($processingOperatorId) {
            case 1:
                return new ArmaxProcessor();
                break;

            case 2:
                return new PompayProcessor();
                break;

            default:
                return null;
                break;
        }
    }
}
