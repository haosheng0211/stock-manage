<?php

use App\Enums;

return [
    Enums\DocumentStatus::class => [
        Enums\DocumentStatus::PROCESS => '處理中',
        Enums\DocumentStatus::SUCCESS => '完成',
        Enums\DocumentStatus::FAILURE => '失敗',
    ],
    Enums\DocumentType::class => [
        Enums\DocumentType::EXPORT => '匯出',
        Enums\DocumentType::IMPORT => '匯入',
    ],
    Enums\SupplierType::class => [
        Enums\SupplierType::NONE     => '尚未設置',
        Enums\SupplierType::ORIGINAL => '原廠',
        Enums\SupplierType::AGENT    => '代理商',
        Enums\SupplierType::TRADER   => '貿易商',
        Enums\SupplierType::FACTORY  => '工廠',
    ],
    Enums\UserPermission::class => [
        Enums\UserPermission::VIEW_USERS            => '檢視使用者',
        Enums\UserPermission::CREATE_USERS          => '新增使用者',
        Enums\UserPermission::UPDATE_USERS          => '編輯使用者',
        Enums\UserPermission::DELETE_USERS          => '刪除使用者',
        Enums\UserPermission::VIEW_PARTS            => '檢視零件',
        Enums\UserPermission::CREATE_PARTS          => '新增零件',
        Enums\UserPermission::UPDATE_PARTS          => '編輯零件',
        Enums\UserPermission::DELETE_PARTS          => '刪除零件',
        Enums\UserPermission::EXPORT_PARTS          => '匯出零件',
        Enums\UserPermission::IMPORT_PARTS          => '匯入零件',
        Enums\UserPermission::VIEW_SUPPLIERS        => '檢視供應商',
        Enums\UserPermission::CREATE_SUPPLIERS      => '新增供應商',
        Enums\UserPermission::UPDATE_SUPPLIERS      => '編輯供應商',
        Enums\UserPermission::DELETE_SUPPLIERS      => '刪除供應商',
        Enums\UserPermission::EXPORT_SUPPLIERS      => '匯出供應商',
        Enums\UserPermission::VIEW_CONTACT_PEOPLE   => '檢視聯絡人',
        Enums\UserPermission::CREATE_CONTACT_PEOPLE => '新增聯絡人',
        Enums\UserPermission::UPDATE_CONTACT_PEOPLE => '編輯聯絡人',
        Enums\UserPermission::DELETE_CONTACT_PEOPLE => '刪除聯絡人',
        Enums\UserPermission::EXPORT_CONTACT_PEOPLE => '匯出聯絡人',
        Enums\UserPermission::SETTING_BACKUP        => '備份設定',
    ],
];
