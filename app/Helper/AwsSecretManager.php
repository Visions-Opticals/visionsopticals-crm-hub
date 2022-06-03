<?php
namespace App\Helper;

use Aws\SecretsManager\SecretsManagerClient;
use Aws\Exception\AwsException;


class AwsSecretManager
{
   public static function getSecret($value)
   {
       $client = new SecretsManagerClient([
           'profile' => 'default',
           'version' => '2017-10-17',
           'region' => 'eu-west-1',
       ]);

       $secretName = $value;

       try {
           $result = $client->getSecretValue([
               'SecretId' => $secretName,
           ]);

       } catch (AwsException $e) {
           $error = $e->getAwsErrorCode();
           if ($error == 'DecryptionFailureException') {
               throw $e;
           }
           if ($error == 'InternalServiceErrorException') {
               throw $e;
           }
           if ($error == 'InvalidParameterException') {
               throw $e;
           }
           if ($error == 'InvalidRequestException') {
               throw $e;
           }
           if ($error == 'ResourceNotFoundException') {
               throw $e;
           }
       }

       if (isset($result['SecretString'])) {
           $secret = $result['SecretString'];

           return $secret;
       } else {
           $secret = base64_decode($result['SecretBinary']);
           return $secret;
       }


   }

}