<?php

namespace HowMAS\CoreMSBundle\Model\HClass;

use Pimcore\Model\Dao\AbstractDao;
use Pimcore\Model\Exception\NotFoundException;
use HowMAS\CoreMSBundle\Service\DatabaseService;

class Dao extends AbstractDao
{
    protected string $tableName = DatabaseService::CLASS_TABLE;

    public function getById(?string $id = null): void
    {
        if ($id !== null)  {
            $this->model->setId($id);
        }

        $data = $this->db->fetchAssociative('SELECT * FROM '. $this->tableName .' WHERE id = ?', [$this->model->getId()]);

        if (!$data) {
            throw new NotFoundException("HClass with the id " . $this->model->getId() . " doesn't exists");
        }

        $this->assignVariablesToModel($data);
    }

    public function getByName(?string $name = null): void
    {
        if ($name !== null)  {
            $this->model->setName($name);
        }

        $data = $this->db->fetchAssociative('SELECT * FROM '. $this->tableName .' WHERE name = ?', [$this->model->getName()]);

        if (!$data) {
            throw new NotFoundException("HClass with the name " . $this->model->getName() . " doesn't exists");
        }

        $this->assignVariablesToModel($data);
    }

    public function save($init = false): void
    {
        $vars = get_object_vars($this->model);

        $buffer = [];

        $validColumns = $this->getValidTableColumns($this->tableName);

        if (count($vars)) {
            foreach ($vars as $k => $v) {
                if (!in_array($k, $validColumns)) {
                    continue;
                }

                $getter = "get" . ucfirst($k);

                if (!is_callable([$this->model, $getter])) {
                    continue;
                }

                $value = $this->model->$getter();

                if (is_bool($value)) {
                    $value = (int)$value;
                }

                $buffer[$k] = $value;
            }
        }

        if (!$init) {
            $this->db->update($this->tableName, $buffer, ["id" => $this->model->getId()]);
            return;
        }

        $this->db->insert($this->tableName, $buffer);
        $this->model->setId($this->db->lastInsertId());
    }

    public function delete(): void
    {
        $this->db->delete($this->tableName, ["id" => $this->model->getId()]);
    }

}