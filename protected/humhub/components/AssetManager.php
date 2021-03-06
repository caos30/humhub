<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2017 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\components;

use Yii;
use yii\base\InvalidParamException;
use yii\helpers\FileHelper;
use yii\web\AssetBundle;

/**
 * AssetManager
 *
 * @inheritdoc
 * @author Luke
 */
class AssetManager extends \yii\web\AssetManager
{
    private $_published = [];

    /**
     * Clears all currently published assets
     */
    public function clear()
    {
        if ($this->basePath == '') {
            return;
        }

        foreach (scandir($this->basePath) as $file) {
            if (substr($file, 0, 1) === '.') {
                continue;
            }
            FileHelper::removeDirectory($this->basePath . DIRECTORY_SEPARATOR . $file);
        }
    }

    public function forcePublish(AssetBundle $bundle, $options = [])
    {
        $options['forceCopy'] = true;

        if ($bundle->sourcePath !== null && !isset($bundle->basePath, $bundle->baseUrl)) {
            $path = Yii::getAlias($bundle->sourcePath);

            if (!is_string($path) || ($src = realpath($path)) === false) {
                throw new InvalidParamException("The file or directory to be published does not exist: $path");
            }

            if (is_file($src)) {
                return $this->publishFile($src);
            } else {
                return $this->publishDirectory($src, $options);
            }
        } else {
            $bundle->publish($this);
        }
    }
}
