<?php

namespace Pim\Bundle\UserBundle\Twig;

use Symfony\Component\Yaml\Yaml;

/**
 * Twig extension to provide acl groups
 *
 * @author    Filips Alpe <filips@akeneo.com>
 * @copyright 2014 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class AclGroupsExtension extends \Twig_Extension
{
    /** @var array */
    protected $bundles;

    /**
     * @param array $bundles
     */
    public function __construct($bundles)
    {
        $this->bundles = $bundles;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'pim_acl_groups_extension';
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('acl_groups', [$this, 'getAclGroups'])
        ];
    }

    /**
     * Get acl groups
     *
     * @return string[]
     */
    public function getAclGroups()
    {
        $config = [];
        foreach ($this->bundles as $class) {
            $reflection = new \ReflectionClass($class);
            $path = dirname($reflection->getFileName()) . '/Resources/config/acl_groups.yml';
            if (file_exists($path)) {
                $config = Yaml::parse(file_get_contents($path)) + $config;
            }
        }

        return $this->getSortedGroupNames($config);
    }

    /**
     * Sort the groups by their order and return the names.
     * If no order is defined for a group, it will be added after the others
     *
     * @param array $config
     *
     * @return string[]
     */
    protected function getSortedGroupNames($config)
    {
        $groups = $this->getGroups($config);
        $groups = $this->sortGroups($groups);

        $groupNames = [];
        foreach ($groups as $group) {
            $groupNames[] = $group['name'];
        }

        return $groupNames;
    }

    /**
     * @param array $config
     *
     * @return array
     */
    protected function getGroups(array $config)
    {
        $groups = [];
        foreach ($config as $groupName => $groupConfig) {
            $groups[] = [
                'name'  => $groupName,
                'order' => isset($groupConfig['order']) ? $groupConfig['order'] : -1
            ];
        }

        return $groups;
    }

    /**
     * @param array $groups
     *
     * @return array
     */
    protected function sortGroups(array $groups)
    {
        usort(
            $groups,
            function ($first, $second) {
                if ($first['order'] === $second['order']) {
                    return 0;
                }

                if ($first['order'] === -1 || $second['order'] === -1) {
                    return ($first['order'] < $second['order']) ? 1 : -1;
                }

                return ($first['order'] < $second['order']) ? -1 : 1;
            }
        );

        return $groups;
    }
}
