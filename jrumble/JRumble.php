<?php
/**
 * User: yiqing
 * Date: 12-5-16
 * Time: 上午1:19
 * To change this template use File | Settings | File Templates.
 *------------------------------------------------------------
 *@see http://jackrugile.com/jrumble/
 *
 * very funny effect
 *
 * more useful jquery plugin :
 * http://slodive.com/web-development/jquery-plugins/
 *------------------------------------------------------------
 */
class JRumble extends CWidget
{


    /**
     * @var string
     */
    public $baseUrl;

    /**
     * @var bool
     */
    public $debug = YII_DEBUG;

    /**
     * @var \CClientScript
     */
    protected $cs;

    /**
     * @var array|string
     * -------------------------
     * the options will be passed to the underlying plugin
     *   eg:  js:{key:val,k2:v2...}
     *   array('key'=>$val,'k'=>v2);
     * -------------------------
     */
    public $options = array();


    /**
     * @var string
     */
    public $selector;

   /**
    * @return JRumble
    */
    public function publishAssets()
    {
        if (empty($this->baseUrl)) {
            $assetsPath = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'assets';
            if ($this->debug == true) {
                $this->baseUrl = Yii::app()->assetManager->publish($assetsPath, false, -1, true);
            } else {
                $this->baseUrl = Yii::app()->assetManager->publish($assetsPath);
            }
        }
        return $this;
    }


   /**
    *
    */
    public function init()
    {

        parent::init();

        $this->cs = Yii::app()->getClientScript();
        // publish assets and register css/js files
        $this->publishAssets();
        // register necessary js file and css files
        $this->cs->registerCoreScript('jquery');

        if($this->debug){
            $this->registerScriptFile('jquery.jrumble.1.3/jquery.jrumble.1.3.js', CClientScript::POS_HEAD);
        }else{
            $this->registerScriptFile('jquery.jrumble.1.3/jquery.jrumble.1.3.min.js', CClientScript::POS_HEAD);
        }


        if (empty($this->selector)) {
            //just register the necessary css and js files ; you want use it manually
            return;
        }

        $options = empty($this->options) ? '' : CJavaScript::encode($this->options);

        $jsSetup = <<<JS_INIT
         $("{$this->selector}").jrumble({$options});
         $("{$this->selector}").hover(function(){
            	$(this).trigger('startRumble');
          }, function(){
	            $(this).trigger('stopRumble');
          });
JS_INIT;
        $this->cs->registerScript(__CLASS__ . '#' . $this->getId(), $jsSetup, CClientScript::POS_READY);

    }


    /**
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        try {
            //shouldn't swallow the parent ' __set operation
            parent::__set($name, $value);
        } catch (Exception $e) {
            $this->options[$name] = $value;
        }
    }

  /**
   * @param $fileName
   * @param int $position
   * @return JRumble
   * @throws InvalidArgumentException
   */
    protected function registerScriptFile($fileName, $position = CClientScript::POS_END)
    {
        if (is_string($fileName)) {
            $jsFiles = explode(',', $fileName);
        } elseif (is_array($fileName)) {
            $jsFiles = $fileName;
        } else {
            throw new InvalidArgumentException('you must give a string or array as first argument , but now you give' . var_export($fileName, true));
        }
        foreach ($jsFiles as $jsFile) {
            $jsFile = trim($jsFile);
            $this->cs->registerScriptFile($this->baseUrl . '/' . ltrim($jsFile, '/'), $position);
        }
        return $this;
    }

   /**
    * @param $fileName
    * @return JRumble
    * @throws InvalidArgumentException
    */
    protected function registerCssFile($fileName)
    {
        $cssFiles = func_get_args();
        foreach ($cssFiles as $cssFile) {
            if (is_string($cssFile)) {
                $cssFiles2 = explode(',', $cssFile);
            } elseif (is_array($cssFile)) {
                $cssFiles2 = $cssFile;
            } else {
                throw new InvalidArgumentException('you must give a string or array as first argument , but now you give' . var_export($cssFiles, true));
            }
            foreach ($cssFiles2 as $css) {
                $this->cs->registerCssFile($this->baseUrl . '/' . ltrim($css, '/'));
            }
        }
        // $this->cs->registerCssFile($this->assetsUrl . '/vendors/' .$fileName);
        return $this;
    }

    /**
     * @static
     * @param bool $hashByName
     * @return string
     * return this widget assetsUrl
     */
    public static function getAssetsUrl($hashByName = false)
    {
        // return CHtml::asset(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'assets', $hashByName);
        return Yii::app()->getAssetManager()->publish(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'assets', $hashByName, -1, YII_DEBUG);
    }
}
