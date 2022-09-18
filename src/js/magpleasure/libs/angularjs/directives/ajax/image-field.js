/**
 * Magpleasure Ltd.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magpleasure.com/LICENSE.txt
 *
 * @category   Magpleasure
 * @package    Magpleasure_Common
 * @copyright  Copyright (c) 2014 Magpleasure Ltd. (http://www.magpleasure.com)
 * @license    http://www.magpleasure.com/LICENSE.txt
 */

var ngImageUploadDirective = ['$http', '$templateCache', '$compile', function ($http, $templateCache, $compile) {
        return {
            restrict: 'C',
            replace: true,
            templateUrl: 'magpleasure-image-field.html',
            scope: {
                mpConfig: "="
            },
            controller: function ($scope) {

                $scope.config = $scope.mpConfig;

                if ($scope.config.has_image) {

                    $scope.file = {
                        'url': $scope.config.url,
                        'thumbnail_url': $scope.config.thumbnail_url,
                        'value': $scope.config.value
                    };

                } else {
                    $scope.file = {};
                }

                $scope.delete = [];

                $scope.options = {
                    fieldName: $scope.config.html_id,
                    change: function (file) {

                        file.$upload($scope.config.upload_url, $scope.file);
                    }
                };

                $scope.showProgress = function () {
                    return !!$scope.file.uploading;
                };

                $scope.hasThumbnail = function () {
                    return !!$scope.file.thumbnail_url;
                }

                $scope.hasValue = function () {
                    return !!$scope.file.value;
                };

                $scope.hasError = function () {
                    return !!$scope.file.error;
                };

                $scope.getRequired = function () {
                    return ($scope.config.is_required && !$scope.file.has_thumbnail);
                };

                $scope.checkImageExistence = function () {
                    $($scope.config.html_id).disabled = $scope.config.has_thumbnail;
                };

                $scope.disableLoader = function () {
                    $scope.loading = false;
                    $scope.loading_percent = 0;
                };

                $scope.startLoader = function () {
                    $scope.error_message = false;
                    $scope.loading_percent = 0;
                    $scope.loading = true;
                };

                $scope.clearData = function () {
                    $scope.delete.push($scope.value);
                    $scope.value = '';
                    $scope.file = {};
                    $scope.checkImageExistence();
                };

                if ($scope.config.has_image) {
                    $scope.config.thumbnail_url = $scope.config.thumbnail_url;
                    $scope.config.image_url = $scope.config.image_url;
                    $scope.config.has_thumbnail = true;
                    $scope.value = $scope.config.value;
                } else {
                    $scope.value = '';
                }
            }
        }

    }]
    ;


Validation.add(
    'required-file',
    'This is a required field.',
    function (value, field) {

        return !Validation.get('IsEmpty') && field.hasClassName('field-ready');
    }
);