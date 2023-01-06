<?php

declare(strict_types=1);

namespace App\Doctrine\Types;

use App\Enum\TypeScrutin;
use Doctrine\DBAL\Platforms\AbstractPlatform;

final class TypeScrutinType extends AbstractEnumType
{
    public const NAME = 'type_scrutin';

    public function getName(): string
    {
        return self::NAME;
    }

    public static function getEnumClass(): string
    {
        return TypeScrutin::class;
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return 'varchar(20)';
    }
}
