<?php

namespace Apto\Base\Domain\Backend\Model\Acl;

class AclIdentity
{
    /**
     * classname of the specified model
     * @var string
     */
    protected $modelClass = null;

    /**
     * surrogate id of the object, if needed
     * @var int|null
     */
    protected $entityId = null;

    /**
     * name of the model's property, if needed
     * @var string|null
     */
    protected $fieldName = null;

    /**
     * AclIdentity constructor.
     * @param string|object $model
     * @param string|null $field
     * @param int|null $id
     */
    public function __construct($model, int $id = null, string $field = null)
    {
        $this
            ->setModelClass($model)
            ->setEntityId($id)
            ->setFieldName($field);
    }

    /**
     * @return string
     */
    public function getModelClass(): string
    {
        return $this->modelClass;
    }

    /**
     * @param string|object $model
     * @return AclIdentity
     * @throws AclIdentityInvalidModelException
     */
    private function setModelClass($model): AclIdentity
    {
        if (null === $model) {
            throw new AclIdentityInvalidModelException('Null is not a valid object or classname.');
        }
        else if (is_string($model)) {
            $this->modelClass = $model;
        }
        else {
            try {
                $this->modelClass = get_class($model);
            }
            catch (\Exception $e)
            {
                throw new AclIdentityInvalidModelException('The given model is neither a string nor an object.');
            }
        }

        return $this;
    }

    /**
     * @return int|null
     */
    public function getEntityId()
    {
        return $this->entityId;
    }

    /**
     * @param int|null $id
     * @return AclIdentity
     * @throws AclIdentityInvalidEntityIdException
     */
    private function setEntityId(int $id = null): AclIdentity
    {
        if (!is_null($id) && !is_int($id) && $id < 0) {
            throw new AclIdentityInvalidEntityIdException('The given entity id \'' . $id . '\' is invalid.');
        }

        $this->entityId = $id;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFieldName()
    {
        return $this->fieldName;
    }

    /**
     * @param string|null $field
     * @return AclIdentity
     * @throws AclIdentityInvalidFieldNameException
     */
    private function setFieldName(string $field = null): AclIdentity
    {
        if ('' === $field) {
            throw new AclIdentityInvalidFieldNameException('The given field name \'' . $field . '\' is invalid.');
        }

        $this->fieldName = $field;

        return $this;
    }

}