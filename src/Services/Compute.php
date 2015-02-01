<?php

namespace PulkitJalan\Google\Services;

class Compute extends AbstractClient
{
    /**
     * @var \Google_Service_Compute
     */
    protected $property;

    /**
     * @var string
     */
    protected $defaultImageProject = 'ubuntu-os-cloud';

    /**
     * @var string
     */
    protected $defaultImage = 'ubuntu-1404';

    /**
     * $param \Google_Client $client
     */
    public function __construct(\Google_Client $client, array $config)
    {
        parent::__construct($client, $config);

        $this->property = new \Google_Service_Compute($this->client);
    }

    /**
     * Create an instance
     *
     * @param  array                            $params
     * @return \Google_Service_Compute_Instance
     */
    public function createInstance(array $params)
    {
        $postBody = new \Google_Service_Compute_Instance();
        $postBody->setName(array_get($params, 'name', $this->getRandomString()));
        $postBody->setDescription(array_get($params, 'description'));
        $postBody->setCanIpForward(array_get($params, 'forwardIp', false));

        $postBody->setMachineType($this->getMachineTypeLink(array_get($params, 'machineType', 'n1-standard-1')));
        $postBody->setScheduling($this->getScheduling($params));

        // set boot disk
        $postBody->setDisks($this->getAttachedDisk(array_merge(['boot' => true], array_get($params, 'disk'))));

        // set network interface, default without ip
        $postBody->setNetworkInterfaces([$this->getNetworkInterface(array_get($params, 'network', []))]);

        // set metadata if provided
        if (array_get($params, 'metadata')) {
            $postBody->setMetadata($this->getMetaData(array_get($params, 'metadata')));
        }

        // set tags if provided
        if (array_get($params, 'tags')) {
            $postBody->setTags($this->getTags(array_get($params, 'tags')));
        }

        // create instance and return
        return $this->property->instances->insert($this->getProject(), $this->getZone(), $postBody, array_get($params, 'opts', []));
    }

    /**
     * List instances
     *
     * @return \Google_Service_Compute_InstanceList
     */
    public function listInstances()
    {
        return $this->property->instances->listInstances($this->getProject(), $this->getZone());
    }

    /**
     * Get a disks
     *
     * @param  array                                $params
     * @return \Google_Service_Compute_AttachedDisk
     */
    protected function getAttachedDisk(array $params)
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

    /**
     * Get a network interface
     *
     * @param  array                                    $params
     * @return \Google_Service_Compute_NetworkInterface
     */
    protected function getNetworkInterface(array $params)
    {
        $networkInterface = new \Google_Service_Compute_NetworkInterface();
        $networkInterface->setNetwork($this->getNetworkLink());

        if (array_get($params, 'ip', false)) {
            $accessConfig = new \Google_Service_Compute_AccessConfig();

            // if ip field is an ipaddress use static ip
            if (filter_var(array_get($params, 'ip'), FILTER_VALIDATE_IP)) {
                $accessConfig->setNatIP(array_get($params, 'ip'));
            }

            $networkInterface->setAccessConfigs([$accessConfig]);
        }

        return $networkInterface;
    }

    /**
     * Get instance metadata
     *
     * @param  array                            $params
     * @return \Google_Service_Compute_Metadata
     */
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

    /**
     * Get instance tags
     *
     * @param  array                        $params
     * @return \Google_Service_Compute_Tags
     */
    protected function getTags(array $params)
    {
        $tags = new \Google_Service_Compute_Tags();
        $tags->setItems($params);

        return $tags;
    }

    /**
     * Get instance scheduling
     *
     * @param  array                              $params
     * @return \Google_Service_Compute_Scheduling
     */
    protected function getScheduling(array $params)
    {
        $scheduling = new \Google_Service_Compute_Scheduling();
        $scheduling->setAutomaticRestart(array_get($params, 'auto_restart', true));
        $scheduling->setOnHostMaintenance(array_get($params, 'host_maintenance', 'migrate'));

        return $scheduling;
    }

    /**
     * Get link for disk type
     *
     * @param  string $diskType default: pd-standard
     * @return string
     */
    protected function getDiskTypeLink($diskType = 'pd-standard')
    {
        return 'https://www.googleapis.com/compute/v1/projects/'.$this->getProject().'/zones/'.$this->getZone().'/diskTypes/'.$diskType;
    }

    /**
     * Get link for maching type
     *
     * @param  string $machineType default: n1-standard-1
     * @return string
     */
    protected function getMachineTypeLink($machineType = 'n1-standard-1')
    {
        return 'https://www.googleapis.com/compute/v1/projects/'.$this->getProject().'/zones/'.$this->getZone().'/machineTypes/'.$machineType;
    }

    /**
     * Get link for project network
     *
     * @param  string $network
     * @return string
     */
    protected function getNetworkLink($network = 'default')
    {
        return 'https://www.googleapis.com/compute/v1/projects/'.$this->getProject().'/global/networks/'.$network;
    }

    /**
     * Get link for image, first check current project
     * else defaults to default images project
     *
     * @param  string $image   default: ubuntu-1404
     * @param  string $project
     * @return string
     */
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
