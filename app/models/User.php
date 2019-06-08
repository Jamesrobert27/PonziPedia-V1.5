<?php

class User extends Model {

	protected $table = 'users';

	protected $guarded = array('id');

	protected $metaAttributes;

	protected $_permissions;

	protected $_role;

	protected function getUsermetaAttribute()
	{
		if (!isset($this->metaAttributes)) {
			$this->metaAttributes = (array) Usermeta::get($this->id, '', true);
		}

		return $this->metaAttributes;
	}

	protected function getDisplayNameAttribute($displayName)
	{
		if (!empty($displayName)) {
			return $displayName;
		}

		if (!empty($this->username)) {
			return $this->username;
		}

		return $this->email;
	}

	protected function getIdAttribute($value)
	{
		return (int) $value;
	}

	protected function getFirstNameAttribute()
	{
		return isset($this->usermeta['first_name']) ? $this->usermeta['first_name'] : '';
	}

	protected function getLastNameAttribute()
	{
		return isset($this->usermeta['last_name']) ? $this->usermeta['last_name'] : '';
	}

	protected function getAboutAttribute()
	{
		return isset($this->usermeta['about']) ? $this->usermeta['about'] : '';
	}

	protected function getGenderAttribute()
	{
		return isset($this->usermeta['gender']) ? $this->usermeta['gender'] : '';
	}

	protected function getBirthdayAttribute()
	{
		return isset($this->usermeta['birthday']) ? $this->usermeta['birthday'] : '';
	}

	protected function getUrlAttribute()
	{
		return isset($this->usermeta['url']) ? $this->usermeta['url'] : '';
	}

	protected function getPhoneAttribute()
	{
		return isset($this->usermeta['phone']) ? $this->usermeta['phone'] : '';
	}

	protected function getLocationAttribute()
	{
		return isset($this->usermeta['location']) ? $this->usermeta['location'] : '';
	}

	protected function getVerifiedAttribute()
	{
		return isset($this->usermeta['verified']) ? $this->usermeta['verified'] : '';
	}

	protected function getLocaleAttribute()
	{
		return isset($this->usermeta['locale']) ? $this->usermeta['locale'] : '';
	}

	protected function getAvatarAttribute()
	{
		return $this->generateAvatar($this->usermeta, $this->email);
	}

	protected function getLastLoginAttribute()
	{
		return isset($this->usermeta['last_login']) ? $this->usermeta['last_login'] : '';
	}

	protected function getLastLoginIpAttribute()
	{
		return isset($this->usermeta['last_login_ip']) ? $this->usermeta['last_login_ip'] : '';
	}

	protected function getRoleAttribute()
	{
		if (!isset($this->_role)) {
			$this->_role = Role::find($this->role_id);
		}

		return $this->_role;
	}

	protected function getPermissionsAttribute()
	{
		if (!isset($this->_permissions) && $this->role) {
			$this->_permissions = explode(',', $this->role->permissions);
		}

		return $this->_permissions;
	}

	public function can($permission)
	{
		return in_array($permission, $this->permissions) || in_array('*', $this->permissions);
	}

	public function getStatusAttribute($value)
	{
		return (int) $value;
	}

	public function isActive()
	{
		return $this->status == 1;
	}

	public function isSuspended()
	{
		return $this->status == 2;
	}

	public static function generateAvatar($meta, $email = '', $type = null)
	{
		if (is_null($type) && isset($meta['avatar_type'])) {
			$type = $meta['avatar_type'];
		}

		switch ($type) {
			case 'image':
				if (!empty($meta['avatar_image'])) {
					return app()->url("uploads/{$meta['avatar_image']}?") . time();
				}

			case 'gravatar':
				return get_gravatar($email, 300, 'mm');

			case 'facebook':
				if (!empty($meta['facebook_id'])) {
					return $meta['facebook_avatar'];
				}

			case 'google':
				if (!empty($meta['google_avatar'])) {
					return str_replace('?sz=50', '?sz=300', $meta['google_avatar']);
				}

			case 'soundcloud':
				if (!empty($meta['soundcloud_avatar'])) {
					return str_replace('-large', '-t300x300', $meta['soundcloud_avatar']);
				}

			default:
				if (!empty($meta["{$type}_avatar"])) {
					return $meta["{$type}_avatar"];
				}
		}

		return asset_url('img/avatar.png');
	}
}
