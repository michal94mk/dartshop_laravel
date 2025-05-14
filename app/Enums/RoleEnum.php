<?php

namespace App\Enums;

enum RoleEnum: string
{
    case Admin = 'admin';
    case Manager = 'manager';
    case User = 'user';
    
    /**
     * Get all role values as array
     *
     * @return array
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
    
    /**
     * Get permissions for this role
     *
     * @return array
     */
    public function permissions(): array
    {
        return match($this) {
            self::Admin => PermissionEnum::values(),
            self::Manager => [
                PermissionEnum::ViewProducts->value,
                PermissionEnum::CreateProducts->value,
                PermissionEnum::EditProducts->value,
                PermissionEnum::ViewCategories->value,
                PermissionEnum::ViewBrands->value,
                PermissionEnum::ViewUsers->value,
                PermissionEnum::ViewPromotions->value,
                PermissionEnum::CreatePromotions->value,
                PermissionEnum::EditPromotions->value,
                PermissionEnum::ViewOrders->value,
                PermissionEnum::ManageOrders->value,
                PermissionEnum::ManageContacts->value,
            ],
            self::User => [
                PermissionEnum::ViewProducts->value,
                PermissionEnum::ViewCategories->value,
                PermissionEnum::ViewBrands->value,
                PermissionEnum::ViewPromotions->value,
            ],
        };
    }
} 