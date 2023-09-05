<?php

namespace App\Service;

use Aws\S3\S3Client;

class AwsS3Service
{
    private $s3Client;

    public function __construct(S3Client $s3Client)
    {
        $this->s3Client = $s3Client;
    }

    public function uploadFile(string $bucketName, string $file_name, $file_temp_src)
    {
        $result = $this->s3Client->putObject([
            'Bucket' => $bucketName,
            'Key' => $file_name,
            'SourceFile' => $file_temp_src
        ]);

        $result_arr = $result->toArray();
        return $result_arr;
    }
}
