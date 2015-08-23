<?php

/**后台管理员视图模型
 * Some rights reserved：85zu.com
 * Contact email:lanbinbin@85zu.com
 */
class UserViewModel extends ViewModel {
    
    public $viewFields = array(
        "User"=>array("*"),
        "Role"=>array("id"=>"role_id","name"=>"role_name","status"=>"role_status","_on"=>"User.role_id=Role.id"),
    );
}
?>
