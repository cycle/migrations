<?php

declare(strict_types=1);

namespace Cycle\Migrations\V2;

trait ColumnTrait
{
    protected function primaryKey($length = null): Column
    {
        $column = new Column(ColumnType::TYPE_PK, $length);
        $column->notNull();

        return $column;
    }

    protected function bigPrimaryKey($length = null): Column
    {
        $column = new Column(ColumnType::TYPE_BIGPK, $length);
        $column->notNull();

        return $column;
    }

    protected function string($length = null): Column
    {
        return new Column(ColumnType::TYPE_STRING, $length);
    }

    protected function text(): Column
    {
        return new Column(ColumnType::TYPE_TEXT);
    }

    protected function integer($length = null): Column
    {
        return new Column(ColumnType::TYPE_INTEGER, $length);
    }

    protected function bigInteger($length = null): Column
    {
        return new Column(ColumnType::TYPE_BIGINT, $length);
    }

    protected function numeric($precision = null): Column
    {
        return new Column(ColumnType::TYPE_NUMERIC, $precision);
    }

    protected function dateTime(): Column
    {
        return new Column(ColumnType::TYPE_DATETIME);
    }

    protected function boolean(): Column
    {
        return new Column(ColumnType::TYPE_BOOLEAN);
    }

    protected function money(): Column
    {
        return new Column(ColumnType::TYPE_MONEY);
    }

    protected function json(): Column
    {
        return new Column(ColumnType::TYPE_JSON);
    }

    protected function point(): Column
    {
        return new Column(ColumnType::TYPE_POINT);
    }

    protected function customType(string $type): Column
    {
        return new Column($type);
    }
}
