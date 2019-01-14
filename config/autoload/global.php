<?php
/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */

return [


        'post' => [
            'attachment' => [
                'maxSizeOfText' => 1024 * 100,
                'allowableMimeTypes' => [
                    'txt' => 'text/plain',
                    'png' => 'image/png',
                    'jpg' => 'image/jpeg',
                    'gif' => 'image/gif'
                ],
            ],
            'fileSettings' => [
                'imageSizes' => [
                    'width' => '320',
                    'height' => '240'
                ],
                'uploadPath' => [
                    'post' => __DIR__.'/../../public/post',
                    'postComments' => __DIR__.'/../../public/post'
                ],
            ],
            'commentPagination' => [
                'itemsPerPage' => 25
            ]
        ]
];
