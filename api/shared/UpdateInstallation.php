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
        $installationId = $inputData["installationId"] ?? $inputData["installation_id"];
        if (empty($installationId) || $installationId === "<UNKNOWN>") {
            throw new InvalidArgumentException("installationId must be set to a valid value. Got: [" . $installationId . "]");
        }

        $deviceId = $inputData["deviceId"] ?? $inputData["device_id"];
        if (empty($deviceId) || $deviceId === -1) {
            throw new InvalidArgumentException("deviceId must be set to a valid value. Got: [" . $deviceId . "]");
        }

        $updateMethodId = $inputData["updateMethodId"] ?? $inputData["update_method_id"];
        if (empty($updateMethodId) || $updateMethodId === -1) {
            throw new InvalidArgumentException("updateMethodId must be set to a valid value. Got: [" . $updateMethodId . "]");
        }

        $this->installationId = $installationId;
        $this->deviceId = $deviceId;
        $this->updateMethodId = $updateMethodId;
        $this->status = $inputData["installationStatus"] ?? $inputData["installation_status"];

        switch ($this->status) {
            case "STARTED" :
                $this->startDate = $inputData["timestamp"];
                $this->startOsVersion = $inputData["startOsVersion"] ?? $inputData["start_os_version"];
                $this->destinationOsVersion = $inputData["destinationOsVersion"] ?? $inputData["destination_os_version"];
                break;
            case "FINISHED":
                $this->lastUpdatedDate = $inputData["timestamp"];
                $this->currentOsVersion = $inputData["currentOsVersion"] ?? $inputData["current_os_version"];
                break;
            case "FAILED":
                $this->lastUpdatedDate = $inputData["timestamp"];
                $this->currentOsVersion = $inputData["currentOsVersion"] ?? $inputData["current_os_version"];
                $this->failureReason = $inputData["failureReason"] ?? $inputData["failure_reason"];
                break;
            default:
                throw new InvalidArgumentException("installationStatus must be set to a valid value. Got: [" . $this->status . "]");
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
