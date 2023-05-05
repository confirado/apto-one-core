<?php

namespace Apto\Base\Domain\Backend\Model\Acl;

class AclMaskBuilder
{
    /**
     * used to map attribute values from AclMask to human readable permissions
     */
    const CREATE = 'create';
    const READ = 'read';
    const UPDATE = 'update';
    const DELETE = 'delete';
    const EXECUTE = 'execute';

    /**
     * @var array
     */
    private $permissionMap = [];

    /**
     * @var array
     */
    private $buildMap = [];

    /**
     * AclMaskBuilder constructor.
     */
    public function __construct()
    {
        $this->permissionMap[self::CREATE] = AclMask::CREATE;
        $this->permissionMap[self::READ] = AclMask::READ;
        $this->permissionMap[self::UPDATE] = AclMask::UPDATE;
        $this->permissionMap[self::DELETE] = AclMask::DELETE;
        $this->permissionMap[self::EXECUTE] = AclMask::EXECUTE;
    }

    /**
     * @param array|string $permission
     */
    public function add($permission)
    {
        if (is_array($permission)) {
            foreach ($permission as $value) {
                $this->addPermission($value);
            }
        } else {
            $this->addPermission($permission);
        }

    }

    /**
     * @return AclMask
     */
    public function get()
    {
        $attributes = 0;
        foreach ($this->buildMap as $attribute) {
            $attributes += $attribute;
        }
        return new AclMask($attributes);
    }

    /**
     * @param string $permission
     */
    private function addPermission(string $permission)
    {
        $permission = strtolower($permission);
        if (array_key_exists($permission, $this->permissionMap)) {
            $this->buildMap[$permission] = $this->permissionMap[$permission];
        }
    }

    /**
     * add mask b to mask a
     * @param AclMask $a
     * @param AclMask $b
     * @return AclMask
     */
    public static function addMasks(AclMask $a, AclMask $b): AclMask
    {
        $mask = $a->getAttributes() | $b->getAttributes();
        return new AclMask($mask);
    }

    /**
     * remove mask b from mask a
     * @param AclMask $a
     * @param AclMask $b
     * @return AclMask
     */
    public static function subtractMasks(AclMask $a, AclMask $b): AclMask
    {
        $mask = $a->getAttributes() & (~$b->getAttributes());
        return new AclMask($mask);
    }
}