<?php

use Illuminate\Database\Seeder;
use App\Model\PermissionName;
use App\Model\PermissionCategory;
class PermissionNameTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $moduleWithPermissionList = array(
            "Customer" => array(
                "permissions" => array(
                    array(
                        "permission_name"=>"Add, Update",
                        "permission_token"=>"customer_add_update"
                    ),
                    array(
                        "permission_name"=>"Delete",
                        "permission_token"=>"customer_delete"
                    ),
                    array(
                        "permission_name"=>"Search customers",
                        "permission_token"=>"customer_search",
                    ),
                    array(
                        "permission_name"=>"Edit Store Account Balance",
                        "permission_token"=>"customer_edit_store_account_balance"
                    ),
                    array(
                        "permission_name"=>"Edit customer points/Number of sales until discount",
                        "permission_token"=>"customer_edit_points"
                    )
                ),
                "description" => "Add, Update, Delete, and Search customers"
            ),

            "Items" => array(
                "permissions"=>array(
                    array("permission_name"=>"Add, Update",
                        "permission_token"=>"item_add_update"
                    ),
                    array(
                        "permission_name"=>"Delete",
                        "permission_token"=>"item_delete"
                    ),
                    array(
                        "permission_name"=>"Search items",
                        "permission_token"=>"item_search",
                    ),
                    array(
                        "permission_name"=>"Manage categories",
                        "permission_token"=>"item_manage_categories"
                    ),
                    array(
                        "permission_name"=>"Manage_tags",
                        "permission_token"=>"item_manage_tags"
                    ),
                    array(
                        "permission_name"=>"Manage manufacturers",
                        "permission_token"=>"item_manage_manufacturers"
                    ),
                ),
                "description"=>"Add, Update, Delete, and Search Items"
            ),

            "Item kits" => array(
                "permissions"=>array(
                    array("permission_name"=>"Add, Update",
                        "permission_token"=>"itemkit_add_update"
                    ),
                    array(
                        "permission_name"=>"Delete",
                        "permission_token"=>"itemkit_delete"
                    ),
                    array(
                        "permission_name"=>"Search item kits",
                        "permission_token"=>"itemkit_search",
                    ),
                    array(
                        "permission_name"=>"See cost price",
                        "permission_token"=>"itemkit_show_cost_price"
                    )
                ),
                "description"=>"Add, Update, Delete, and Search Item Kits"
            ),

            "Price rules" => array(
                "permissions"=>array(
                    array("permission_name"=>"Add, Update",
                        "permission_token"=>"price_rules_add_update"
                    ),
                    array(
                        "permission_name"=>"Delete",
                        "permission_token"=>"price_rules_delete"
                    ),
                    array(
                        "permission_name"=>"Search price rules",
                        "permission_token"=>"price_rules_search",
                    )
                ),
                "description"=>"Add, Update, Delete, and Search Pricing Rules"
            ),

            "Suppliers" => array(
                "permissions"=>array(
                    array("permission_name"=>"Add, Update",
                        "permission_token"=>"suppliers_add_update"
                    ),
                    array(
                        "permission_name"=>"Delete",
                        "permission_token"=>"suppliers_delete"
                    ),
                    array(
                        "permission_name"=>"Search price rules",
                        "permission_token"=>"suppliers_search",
                    ),
                    array(
                        "permission_name"=>"Edit Store Account Balance",
                        "permission_token"=>"suppliers_edit_store_account_balance",
                    )
                ),
                "description"=>"Add, Update, Delete, and Search Suppliers"
            ),
            "Reports" => array(
                "permissions"=>array(
                    array("permission_name"=>"Categories",
                        "permission_token"=>"report_categories"
                    ),
                    array(
                        "permission_name"=>"Closeout",
                        "permission_token"=>"report_closeout"
                    ),
                    array(
                        "permission_name"=>"Custom Report",
                        "permission_token"=>"report_custom",
                    ),
                    array(
                        "permission_name"=>"Customers",
                        "permission_token"=>"report_customers",
                    ),
                    array(
                        "permission_name"=>"Discounts",
                        "permission_token"=>"report_discounts",
                    ),
                    array(
                        "permission_name"=>"Employees",
                        "permission_token"=>"report_employees",
                    ),
                    array(
                        "permission_name"=>"Inventory",
                        "permission_token"=>"report_inventory",
                    ),
                    array(
                        "permission_name"=>"Item Kits",
                        "permission_token"=>"report_item_kits",
                    ),
                    array(
                        "permission_name"=>"Items",
                        "permission_token"=>"report_items",
                    ),
                    array(
                        "permission_name"=>"Manufacturers",
                        "permission_token"=>"report_manufacturers",
                    ),
                    array(
                        "permission_name"=>"Payments",
                        "permission_token"=>"report_payments",
                    ),
                    array(
                        "permission_name"=>"Profit and Loss",
                        "permission_token"=>"report_profit_loss",
                    ),
                    array(
                        "permission_name"=>"Sales",
                        "permission_token"=>"report_sales",
                    ),
                    array(
                        "permission_name"=>"Suppliers",
                        "permission_token"=>"report_suppliers",
                    ),
                    array(
                        "permission_name"=>"Suspended Sales",
                        "permission_token"=>"report_suspended_sales",
                    ),
                ),
                "description"=>"View and generate reports"
            ),
            "Sales" => array(
                "permissions"=>array(

                    array(
                        "permission_name"=>"Add, Update",
                        "permission_token"=>"sale_add_update"
                    ),
                    array(
                        "permission_name"=>"Delete Suspended Sale",
                        "permission_token"=>"delete_suspended_sale",
                    ),
                    array(
                        "permission_name"=>"Edit Sale",
                        "permission_token"=>"sale_add_update",
                    ),
                    array(
                        "permission_name"=>"Delete Sale",
                        "permission_token"=>"sale_delete",
                    ),
                    array(
                        "permission_name"=>"Search Sale",
                        "permission_token"=>"sale_search",
                    ),
                    array(
                        "permission_name"=>"Sale Reciept",
                        "permission_token"=>"sale_receipt",
                    ),
                ),
                "description"=>" Process sales and returns"
            ),
            "Employees" => array(
                "permissions"=>array(
                    array("permission_name"=>"Add, Update",
                        "permission_token"=>"employees_add_update"
                    ),
                    array(
                        "permission_name"=>"Delete",
                        "permission_token"=>"employees_delete"
                    ),
                    array(
                        "permission_name"=>"Search employees",
                        "permission_token"=>"employees_search",
                    ),
                    array(
                        "permission_name"=>"Edit Profile",
                        "permission_token"=>"employees_edit_profile",
                    ),
                ),
                "description"=>"Add, Update, Delete, and Search employees"
            ),
            "Gift Cards" => array(
                "permissions"=>array(
                    array("permission_name"=>"Add, Update",
                        "permission_token"=>"gift_cards_add_update"
                    ),
                    array(
                        "permission_name"=>"Delete",
                        "permission_token"=>"gift_cards_delete"
                    ),
                    array(
                        "permission_name"=>"Use Gift Card",
                        "permission_token"=>"gift_card_use",
                    ),

                ),
                "description"=>"Add, Update, Delete, Search and Use Gift Cards"
            ),
            "Counters" => array(
                "permissions"=>array(
                    array(
                        "permission_name"=>"Add, Update",
                        "permission_token"=>"counters_add_update"
                    ),
                    array(
                        "permission_name"=>"Delete",
                        "permission_token"=>"counters_delete"
                    )
                ),
                "description"=>"Add, Update, Delete Counters"
            ),
        );


        foreach($moduleWithPermissionList as $module=>$definitions){

            $permissionCategory = PermissionCategory::where('permission_category_name', '=' , $module )->first();
            if (!$permissionCategory) {

                $data['permission_category_name'] = $module;
                $data['permission_category_description'] = $definitions['description'];
                $permissionCategoryToInsert = new PermissionCategory();
                $permissionCategoryId = $permissionCategoryToInsert->AddPermissionCategory($data);


            }else{
                $permissionCategoryId = $permissionCategory->id;
            }

            foreach($definitions as $permissions){
                if(is_array($permissions))
                    foreach($permissions as $aPermission){

                        $permissionName = PermissionName::where("permission_token",$aPermission["permission_token"])->first();
                        if(!is_null($permissionName)){
                            $data['permission_name'] = $aPermission['permission_name'];
                            $data['permission_token'] = $aPermission['permission_token'];
                            $data['permission_category_id'] = $permissionCategoryId;

                            $permissionName->UpdatePermission($data);
                        }else{
                            $permissionName = new PermissionName();
                            $data['permission_name'] = $aPermission['permission_name'];
                            $data['permission_token'] = $aPermission['permission_token'];
                            $data['permission_category_id'] = $permissionCategoryId;

                            $permissionName->AddPermission($data);
                        }

                    }
            }

//            $categoryPermissionList = PermissionName::where('permission_category_id','=',$permissionCategoryId)->get();
//
//            $isCategoryPermissionListEmpty = $categoryPermissionList->isEmpty();
//
//            $permissionName = new PermissionName();



//            if($isCategoryPermissionListEmpty){
//
//                foreach($definitions as $permissions){
//                    if(is_array($permissions))
//                    foreach($permissions as $aPermission){
//                        $data['permission_name'] = $aPermission['permission_name'];
//                        $data['permission_token'] = $aPermission['permission_token'];
//                        $data['permission_category_id'] = $permissionCategoryId;
//
//                        $permissionName->AddPermission($data);
//                    }
//                }
//            }else{
//
//            }
//        }
        }


    }
}
