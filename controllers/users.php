<?

/*     
    Copyright 2012 OpenBroadcaster, Inc.

    This file is part of OpenBroadcaster Server.

    OpenBroadcaster Server is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    OpenBroadcaster Server is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Affero General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with OpenBroadcaster Server.  If not, see <http://www.gnu.org/licenses/>.
*/

class Users extends OBFController
{

  public function __construct()
  {
    parent::__construct();
    $this->user->require_authenticated();

    $this->UsersModel = $this->load->model('users');
  }

  public function user_list()
  {
    $this->user->require_permission('manage_users or manage_schedule_permissions:*');
    $users = $this->UsersModel('user_list');
    return array(true,'User list.',$users);
  }

  public function user_manage_list()
  {
    $this->user->require_permission('manage_users');

    $sort_col = $this->data('sort_col');
    $sort_desc = $this->data('sort_desc');

    if(array_search($sort_col,array('display_name','email','created','last_access'))===false) $sort_col='display_name';

    $users = $this->UsersModel('user_manage_list',$sort_col,($sort_desc ? 'desc' : 'asc'));
    return array(true,'User list.',$users);
  }

  public function user_manage_addedit()
  {
    $this->user->require_permission('manage_users');

    $data = array();

    $data['name'] = trim($this->data('name'));
    $data['username'] = trim($this->data('username'));
    $data['email'] = trim($this->data('email'));
    $data['display_name'] = trim($this->data('display_name'));
    $data['enabled'] = trim($this->data('enabled'));

    $data['password'] = trim($this->data('password'));
    $data['password_confirm'] = trim($this->data('password_confirm'));

    $id = trim($this->data('id'));

    $data['group_ids'] = $this->data('group_ids');

    $validation = $this->UsersModel('user_validate',$data,$id);
    if($validation[0]==false) return $validation;

    $this->UsersModel('user_save',$data,$id);   

    return array(true,'User has been saved.');
  }

  public function user_manage_delete()
  {
    $this->user->require_permission('manage_users');

    $id = $this->data('id');
    
    if(empty($id)) return array(false,'Invalid User ID.');

    $this->UsersModel('user_delete',$id);

    return array(true,'User deleted.');
  }

  public function group_list()
  {
    $this->user->require_permission('manage_users or manage_permissions');
    $groups = $this->UsersModel('group_list');
    return array(true,'Group list.',$groups);
  }


  public function permissions_manage_delete()
  {
    $this->user->require_permission('manage_permissions');

    $id = trim($this->data('id'));

    if(!empty($id)) 
    {
      $this->UsersModel('group_delete',$id);
    }

    return array(true,'Group deleted.');
  }

  public function permissions_manage_addedit()
  {
    $this->user->require_permission('manage_permissions');

    $data['name'] = trim($this->data('name'));
    $id = trim($this->data('id'));
    $data['permissions'] = $this->data('permissions');

    $validation = $this->UsersModel('group_validate',$data,$id);
    if($validation[0]==false) return $validation;

    // proceed with add/edit. 
    $this->UsersModel('group_save',$data,$id);

    return array(true,'Group saved.');
  }

  public function permissions_manage_list()
  {
    $this->user->require_permission('manage_permissions');
    $permissions = $this->UsersModel('permissions_list');
    return array(true,'Permisisons list.',$permissions);
  }

}
