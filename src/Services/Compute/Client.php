<?php

namespace PulkitJalan\Google\Services\Compute;

use PulkitJalan\Google\Services\AbstractClient;

class Client extends AbstractClient
{
    /**
     * @var \Google_Service_Compute
     */
    protected $compute;

    protected $defaultImageProject = 'ubuntu-os-cloud';
    protected $defaultImage = 'ubuntu-1404';

    /**
     * $param \Google_Client $client
     */
    public function __construct(\Google_Client $client, array $config)
    {
        parent::__construct($client, $config);

        $this->compute = new \Google_Service_Compute($this->client);
    }

    public function createInstance(array $params)
    {
        $postBody = new \Google_Service_Compute_Instance();
        $postBody->setName(array_get($params, 'name', $this->getRandomString()));
        $postBody->setCanIpForward(array_get($params, 'forwardIp', false));
        $postBody->setDescription(array_get($params, 'description', ''));
        $postBody->setMachineType($this->getMachineTypeLink(array_get($params, 'machineType', 'n1-standard-1')));
        $postBody->setScheduling($this->getScheduling($params));

        // set disks, default to at boot disk only
        $postBody->setDisks($this->getDisks(array_get($params, 'disks', ['boot' => []])));

        // set network interface, default without ip
        $postBody->setNetworkInterfaces([$this->getNetworkInterface(array_get($params, 'network', []))]);

        if (array_get($params, 'metadata')) {
            $postBody->setMetadata($this->getMetaData(array_get($params, 'metadata')));
        }

        if (array_get($params, 'tags')) {
            $postBody->setTags($this->getTags(array_get($params, 'tags')));
        }

        return $this->compute->instances->insert($this->getProject(), $this->getZone(), $postBody);
    }

    public function listInstances(array $params)
    {
        return $this->compute->instances->listInstances($this->getProject(), $this->getZone());
    }

    protected function getDisks(array $params)
    {
        $disks = [];
        foreach ((array) $params as $key => $value) {
            if ($key === 'boot') {
                $value['boot'] = true;
            }
            $disks[] = $this->getDisk($value);
        }

        return $disks;
    }

    protected function getDisk(array $params)
    {
        $disk = new \Google_Service_Compute_AttachedDisk();
        $disk->setBoot(array_get($params, 'boot', false));
        $disk->setAutoDelete(array_get($params, 'auto_delete', true));

        if (array_get($params, 'boot', false)) {
            $diskInit = new \Google_Service_Compute_AttachedDiskInitializeParams();
            $diskInit->setDiskName(array_get($params, 'name'));
            $diskInit->setDiskSizeGb(array_get($params, 'size', 10));
            $diskInit->setDiskType($this->getDiskTypeLink(array_get($params, 'type', 'pd-standard')));
            $diskInit->setSourceImage($this->getImageLink(array_get($params, 'image', $this->defaultImage)));

            $disk->setInitializeParams($diskInit);
        }

        return $disk;
    }

    protected function getNetworkInterface(array $params)
    {
        $networkInterface = new \Google_Service_Compute_NetworkInterface();
        $networkInterface->setNetwork($this->getNetworkLink());

        if (array_get($params, 'ip', false)) {
            $accessConfig = new \Google_Service_Compute_AccessConfig();
            if (filter_var(array_get($params, 'ip'), FILTER_VALIDATE_IP)) {
                $accessConfig->setNatIP(array_get($params, 'ip'));
            }
            $networkInterface->setAccessConfigs([$accessConfig]);
        }

        return $networkInterface;
    }

    protected function getMetaData(array $params)
    {
        $metaData = new \Google_Service_Compute_Metadata();
        $metaDataItems = [];
        foreach ((array) $params as $key => $value) {
            $item = new \Google_Service_Compute_MetadataItems();
            $item->setKey($key);
            $item->setValue($value);
            $metaDataItems[] = $item;
        }
        $metaData->setItems($metaDataItems);

        return $metaData;
    }

    protected function getTags(array $params)
    {
        $tags = new \Google_Service_Compute_Tags();
        $tags->setItems($params);

        return $tags;
    }

    protected function getScheduling(array $params)
    {
        $scheduling = new \Google_Service_Compute_Scheduling();
        $scheduling->setAutomaticRestart(array_get($params, 'auto_restart', true));
        $scheduling->setOnHostMaintenance(array_get($params, 'host_maintenance', 'migrate'));

        return $scheduling;
    }

    protected function getDiskTypeLink($diskType = 'pd-standard')
    {
        return 'https://www.googleapis.com/compute/v1/projects/'.$this->getProject().'/zones/'.$this->getZone().'/diskTypes/'.$diskType;
    }

    protected function getMachineTypeLink($machineType = 'n1-standard-1')
    {
        return 'https://www.googleapis.com/compute/v1/projects/'.$this->getProject().'/zones/'.$this->getZone().'/machineTypes/'.$machineType;
    }

    protected function getNetworkLink($network = 'default')
    {
        return 'https://www.googleapis.com/compute/v1/projects/'.$this->getProject().'/global/networks/'.$network;
    }

    protected function getImageLink($image = 'ubuntu-1404', $project = null)
    {
        $project = $project ?: $this->getProject();

        $images = $this->compute->images->listImages($project);
        foreach ((array) $images->getItems() as $item) {
            // ignore deprecated items
            if ($item->getDeprecated()) {
                continue;
            }

            // check if name matches image
            if (str_contains($item->getName(), $image)) {
                return $item->getSelfLink();
            }
        }

        // default to default image and default project
        return $this->getImageLink($this->defaultImage, $this->defaultImageProject);
    }
}
