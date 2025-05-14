<?php

namespace App\Enums;

enum PermissionEnum: string
{
    // Products
    case ViewProducts = 'view products';
    case CreateProducts = 'create products';
    case EditProducts = 'edit products';
    case DeleteProducts = 'delete products';
    
    // Categories
    case ViewCategories = 'view categories';
    case CreateCategories = 'create categories';
    case EditCategories = 'edit categories';
    case DeleteCategories = 'delete categories';
    
    // Brands
    case ViewBrands = 'view brands';
    case CreateBrands = 'create brands';
    case EditBrands = 'edit brands';
    case DeleteBrands = 'delete brands';
    
    // Users
    case ViewUsers = 'view users';
    case CreateUsers = 'create users';
    case EditUsers = 'edit users';
    case DeleteUsers = 'delete users';
    
    // Promotions
    case ViewPromotions = 'view promotions';
    case CreatePromotions = 'create promotions';
    case EditPromotions = 'edit promotions';
    case DeletePromotions = 'delete promotions';
    
    // Orders
    case ViewOrders = 'view orders';
    case ManageOrders = 'manage orders';
    
    // Contact
    case ManageContacts = 'manage contacts';
    
    /**
     * Get all permission values as array
     *
     * @return array
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
} 