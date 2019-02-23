<?php

class UpdateInstallation
{
    private $installationId;
    private $deviceId;
    private $updateMethodId;
    private $status;
    private $startDate;
    private $lastUpdatedDate;
    private $startOsVersion;
    private $destinationOsVersion;
    private $currentOsVersion;
    private $failureReason;

    public function __construct($inputData)
    {
        if (empty($inputData["installationId"]) || $inputData["installationId"] === "<UNKNOWN>") {
            throw new InvalidArgumentException("installationID must be set to a valid value. Got: [" . $inputData["installationId"] . "]");
        }

        if (empty($inputData["deviceId"]) || $inputData["deviceId"] === -1) {
            throw new InvalidArgumentException("deviceID must be set to a valid value. Got: [" . $inputData["deviceId"] . "]");
        }

        if (empty($inputData["updateMethodId"]) || $inputData["updateMethodId"] === -1) {
            throw new InvalidArgumentException("updateMethodId must be set to a valid value. Got: [" . $inputData["updateMethodId"] . "]");
        }

        $this->installationId = $inputData["installationId"];
        $this->deviceId = $inputData["deviceId"];
        $this->updateMethodId = $inputData["updateMethodId"];
        $this->status = $inputData["installationStatus"];

        switch ($inputData["installationStatus"]) {
            case "STARTED" :
                $this->startDate = $inputData["timestamp"];
                $this->startOsVersion = $inputData["startOsVersion"];
                $this->destinationOsVersion = $inputData["destinationOsVersion"];
                break;
            case "FINISHED":
                $this->lastUpdatedDate = $inputData["timestamp"];
                $this->currentOsVersion = $inputData["currentOsVersion"];
                break;
            case "FAILED":
                $this->lastUpdatedDate = $inputData["timestamp"];
                $this->currentOsVersion = $inputData["currentOsVersion"];
                $this->failureReason = $inputData["failureReason"];
                break;
            default:
                throw new InvalidArgumentException("installationStatus must be set to a valid value. Got: [" . $inputData["installationStatus"] . "]");
        }
    }

    /**
     * @return mixed
     */
    public function getInstallationId()
    {
        return $this->installationId;
    }

    /**
     * @return mixed
     */
    public function getDeviceId()
    {
        return $this->deviceId;
    }

    /**
     * @return mixed
     */
    public function getUpdateMethodId()
    {
        return $this->updateMethodId;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return mixed
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @return mixed
     */
    public function getLastUpdatedDate()
    {
        return $this->lastUpdatedDate;
    }

    /**
     * @return mixed
     */
    public function getStartOsVersion()
    {
        return $this->startOsVersion;
    }

    /**
     * @return mixed
     */
    public function getDestinationOsVersion()
    {
        return $this->destinationOsVersion;
    }

    /**
     * @return mixed
     */
    public function getCurrentOsVersion()
    {
        return $this->currentOsVersion;
    }

    /**
     * @return mixed
     */
    public function getFailureReason()
    {
        return $this->failureReason;
    }
}