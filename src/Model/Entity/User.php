<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Authentication\PasswordHasher\DefaultPasswordHasher;
use Cake\Core\Configure;
use Cake\ORM\Entity;

/**
 * User Entity
 *
 * @property int $id
 * @property bool $is_admin
 * @property bool $is_verified
 * @property int $phone
 * @property string $address
 * @property string $email
 * @property string $name
 * @property string $password
 * @property string $zipcode
 * @property string|null $reset_password_token
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 * @property bool $is_super_admin
 *
 * @property \App\Model\Entity\Project[] $projects
 * @property \App\Model\Entity\Message[] $messages
 * @property \App\Model\Entity\Note[] $notes
 * @property \App\Model\Entity\Vote[] $votes
 * @property \App\Model\Entity\Bio $bio
 */
class User extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        'name' => true,
        'email' => true,
        'password' => true,
        'phone' => true,
        'is_admin' => true,
        'is_verified' => true,
        'created' => true,
        'modified' => true,
        'projects' => true,
        'messages' => true,
        'notes' => true,
        'votes' => true,
        'reset_password_token' => true,
        'token_created_date' => true,
        'address' => true,
        'zipcode' => true,
    ];

    /**
     * Fields that are excluded from JSON versions of the entity.
     *
     * @var array
     */
    protected $_hidden = [
        'password',
    ];

    /**
     * Automatically hashes password
     *
     * @param string $password Password
     * @return bool|string
     */
    protected function _setPassword(string $password)
    {
        if (strlen($password) > 0) {
            return (new DefaultPasswordHasher())->hash($password);
        }

        return $password;
    }

    /**
     * Strips out non-numeric strings from a phone number
     *
     * @param string|int $phone
     * @return string
     */
    public static function cleanPhone($phone)
    {
        return preg_replace('/[^0-9]/', '', (string)$phone);
    }

    protected function _setPhone($phone)
    {
        $phone = self::cleanPhone($phone);

        $phoneUpdated = $phone != $this->phone;
        if ($phoneUpdated) {
            $this->is_verified = false;
        }

        return $phone;
    }

    protected function _getIsSuperAdmin(): bool
    {
        return $this->id == Configure::read('superAdminUserId');
    }
}
