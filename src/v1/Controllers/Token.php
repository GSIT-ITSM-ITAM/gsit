<?php

namespace App\v1\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use DateTime;
use Firebase\JWT\JWT;
use Tuupola\Base62;

final class Token
{
   /**
    * Check is a password match the stored hash
    *
    * @since 0.85
    *
    * @param string $pass Password (pain-text)
    * @param string $hash Hash
    *
    * @return boolean
    */
  public static function checkPassword($pass, $hash)
  {
    $tmp = password_get_info($hash);
    $verify = false;

    if (isset($tmp['algo']) && $tmp['algo'])
    {
      $verify = password_verify($pass, $hash);
    }
    elseif (strlen($hash) == 32)
    {
      $verify = md5($pass) === $hash;
    }
    elseif (strlen($hash) == 40)
    {
      $verify = sha1($pass) === $hash;
    }
    else
    {
      $salt = substr($hash, 0, 8);
      $verify = ($salt . sha1($salt . $pass) === $hash);
    }
    return $verify;
  }

  public function generateJWTToken(
    \App\Models\User $user,
    $profile_id = null,
    $entity_id = null,
    $entity_recursive = false
  )
  {
    global $basePath;

    $firstName = $user->firstname;
    $lastName = $user->lastname;
    // $jwtid = $user->getPropertyAttribute('userjwtid');
    $jwtid = null;
    // $jwtidId = $user->getPropertyAttribute('userjwtid', 'id');
    // $refreshtokenPropId = $user->getPropertyAttribute('userrefreshtoken', 'id');
    // if (is_null($jwtidId) || is_null($refreshtokenPropId))
    // {
    //   throw new \Exception('The database is corrupted', 500);
    // }

    // Generate a new refreshtoken and save in DB
    $refreshtoken = $this->generateToken();
    // $user->properties()->updateExistingPivot($refreshtokenPropId, ['value_string' => $refreshtoken]);

    // the jwtid (jit), used to revoke the JWT by server (for example when change rights, disable user...)
    if (is_null($jwtid))
    {
      $jti = $this->generateToken();
      // $user->properties()->updateExistingPivot($jwtidId, ['value_string' => $jti]);
    }
    else
    {
      $jti = $jwtid;
    }

    if (is_null($profile_id))
    {
      foreach ($user->profiles()->get() as $profile)
      {
        $profile_id = $profile->id;
        $entity_id = $profile->pivot->entity_id;
        $entity_recursive = $profile->pivot->is_recursive;
        break;
      }
    }
    $entity = \App\Models\Entity::find($entity_id);

    if (is_null($profile_id) || is_null($entity_id))
    {
      header('Location: ' . $basePath);
      exit();
    }

    $now = new DateTime();
    $future = new DateTime("+2000 minutes");
    // For test / DEBUG
    // $future = new DateTime("+30 seconds");
    // Get roles
    // $role = $user->roles()->first();

    // if (is_null($role))
    // {
    //   throw new \Exception('No role assigned to the user', 401);
    // }

    $payload = [
      'iat'              => $now->getTimeStamp(),
      'exp'              => $future->getTimeStamp(),
      'jti'              => $jti,
      'sub'              => '',
      'scope'            => $this->getScope($user->id),
      'user_id'          => $user->id,
      // 'role_id'          => $role->id,
      'firstname'        => $firstName,
      'lastname'         => $lastName,
      'apiversion'       => "v1",
      // 'entities_id'      => $user->entities_id,
      'sub_organization' => true,
      'profile_id'       => $profile_id,
      'entity_id'        => $entity_id,
      'entity_treepath'  => $entity->treepath,
      'entity_recursive' => $entity_recursive,
    ];
    // $configSecret = include(__DIR__ . '/../../../config/current/config.php');
    $secret = sodium_base642bin('TEST', SODIUM_BASE64_VARIANT_ORIGINAL);
    $token = JWT::encode($payload, $secret, "HS256");
    $responseData = [
      "token"        => $token,
      "refreshtoken" => $refreshtoken,
      "expires"      => $future->getTimeStamp()
    ];
    return $responseData;
  }

  // get rights of this user.
  private function getScope($userId)
  {
    $scope = [
    ];

    return $scope;
  }

  private function generateToken()
  {
     return (new Base62())->encode(random_bytes(16));
  }
}
