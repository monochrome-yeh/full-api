<?php
namespace common\components\monochrome\bootstrap;

use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use common\components\monochrome\bootstrap\Menu;
use yii\bootstrap\BootstrapPluginAsset;
use yii\bootstrap\BootstrapAsset;
use yii\bootstrap\Widget;
use yii\helpers\Html;

class SlideNav extends Widget
{
    public $items = [];
    /**
     * @var boolean whether the nav items labels should be HTML-encoded.
     */
    public $encodeLabels = true;
    /**
     * @var boolean whether to automatically activate items according to whether their route setting
     * matches the currently requested route.
     * @see isItemActive
     */
    public $activateItems = true;
    /**
     * @var boolean whether to activate parent menu items when one of the corresponding child menu items is active.
     */
    public $activateParents = false;
    /**
     * @var string the route used to determine if a menu item is active or not.
     * If not set, it will use the route of the current request.
     * @see params
     * @see isItemActive
     */
    public $route;
    /**
     * @var array the parameters used to determine if a menu item is active or not.
     * If not set, it will use `$_GET`.
     * @see route
     * @see isItemActive
     */
    public $params;
    /**
     * @var string this property allows you to customize the HTML which is used to generate the drop down caret symbol,
     * which is displayed next to the button text to indicate the drop down functionality.
     * Defaults to `null` which means `<b class="caret"></b>` will be used. To disable the caret, set this property to be an empty string.
     */
    public $dropDownCaret;

    public $brandLabel;

    public $header_items;

    public $menu_label;

    public $avatarUrl;

    public $username;
    /**
     * Initializes the widget.
     */
    public function init()
    {
        parent::init();
        if ($this->route === null && Yii::$app->controller !== null) {
            $this->route = Yii::$app->controller->getRoute();
        }
        if ($this->params === null) {
            $this->params = Yii::$app->request->getQueryParams();
        }
        if ($this->dropDownCaret === null) {
            $this->dropDownCaret = Html::tag('span', '', ['class' => 'fa arrow']);
        }
        Html::addCssClass($this->options, 'nav');
    }

    /**
     * Renders the widget.
     */
    public function run()
    {
        BootstrapAsset::register($this->getView());
        return $this->renderItems();
    }

    /**
     * Renders widget items.
     */
    public function renderItems()
    {
        $items = [];
        $dropdown_toggle = '';
        $header_items = '';
        //增加slidre nav 的li nav-header
        
        if (!empty($this->avatarUrl)) {
            $avatar = Html::tag('img', '', ['alt' => 'avatar', 'class' => 'img-circle', 'src' => $this->avatarUrl, 'width' => 48]);                        
        }
        else {
            $avatar = Html::tag('div', '', ['class' => 'fa fa-male fa-2x text-white' ]);                        
        }
        $userNameTag = Html::tag('span', Html::tag('span','<strong class="font-bold">'.$this->username.'</strong>', ['class' => 'block m-t-xs']), ['class' => 'clear']);
        
        if ($this->menu_label != null) {
        
            $menu_caret = !empty($this->header_items) ? '<b class="caret"></b>' : '';    
            $menu_label = '<span class="text-muted text-xs block">'. $this->menu_label .$menu_caret.'</span>';
            $dropdown_toggle = Html::tag('a', $userNameTag.$menu_label, ['data-toggle' => "dropdown", 'class' => 'dropdown-toggle', 'href' => '#']);
            
            

            if(!empty($this->header_items)) {
                $header_items .= '<ul class="dropdown-menu animated fadeInRight m-t-xs">';
                foreach ($this->header_items as $value) {
                    if(is_array($value)) {
                        $linkOptions = empty($value['linkOptions']) ? [] : $value['linkOptions'];
                        $options = empty($value['options']) ? [] : $value['options'];
                        $_item = Html::a($value['label'], $value['url'], $linkOptions);
                        $header_items .= Html::tag('li', $_item, $options);                   
                    }
                    else {
                        $header_items .= $value;
                    }

                }
                $header_items .= '</ul>';
            }
        }
        else {
            $dropdown_toggle = Html::tag('a', $userNameTag, ['data-toggle' => "dropdown", 'class' => 'dropdown-toggle', 'href' => '#']);
        }

        $headerContent = Html::tag('span', $avatar). $dropdown_toggle.$header_items;

        $items[] = Html::tag('li',
            Html::tag('div', $headerContent, ['class' => 'dropdown profile-element']).
            Html::tag('div', $userNameTag.'<span class="text-xs block">'. $this->menu_label.'</span>', ['class' => 'logo-element'])
        , ['class' => 'nav-header']);

        foreach ($this->items as $i => $item) {
            if (isset($item['visible']) && !$item['visible']) {
                continue;
            }
            $items[] = $this->renderItem($item);
        }

        return Html::tag('ul', implode("\n", $items), $this->options);
    }

    /**
     * Renders a widget's item.
     * @param string|array $item the item to render.
     * @return string the rendering result.
     * @throws InvalidConfigException
     */
    public function renderItem($item)
    {
        if (is_string($item)) {
            return $item;
        }
        if (!isset($item['label'])) {
            throw new InvalidConfigException("The 'label' option is required.");
        }
        $encodeLabel = isset($item['encode']) ? $item['encode'] : $this->encodeLabels;
        $label = $encodeLabel ? Html::encode($item['label']) : $item['label'];
        $options = ArrayHelper::getValue($item, 'options', []);
        $items = ArrayHelper::getValue($item, 'items');
        $url = ArrayHelper::getValue($item, 'url', '#');
        $linkOptions = ArrayHelper::getValue($item, 'linkOptions', []);

        if (isset($item['active'])) {
            $active = ArrayHelper::remove($item, 'active', false);
        } else {
            $active = $this->isItemActive($item);
        }

        if ($items !== null) {
            // $linkOptions['data-toggle'] = 'collapse';
            Html::addCssClass($options, 'dropdown');
            // Html::addCssClass($linkOptions, 'dropdown-toggle');
            if ($this->dropDownCaret !== '') {
                $label .= ' ' . $this->dropDownCaret;
            }
            if (is_array($items)) {
                if ($this->activateItems) {
                    $items = $this->isChildActive($items, $active);
                }
                $items = $this->renderDropdown($items, $item);
            }
        }

        if ($this->activateItems && $active) {
            Html::addCssClass($options, 'active');
        }

        return Html::tag('li', Html::a($label, $url, $linkOptions) . $items, $options);
    }

    /**
     * Renders the given items as a dropdown.
     * This method is called to create sub-menus.
     * @param array $items the given items. Please refer to [[Dropdown::items]] for the array structure.
     * @param array $parentItem the parent item information. Please refer to [[items]] for the structure of this array.
     * @return string the rendering result.
     * @since 2.0.1
     */
    protected function renderDropdown($items, $parentItem)
    {
        return Menu::widget([
            'items' => $items,
            'encodeLabels' => $this->encodeLabels,
            'clientOptions' => false,
            'view' => $this->getView(),
        ]);
    }

    /**
     * Check to see if a child item is active optionally activating the parent.
     * @param array $items @see items
     * @param boolean $active should the parent be active too
     * @return array @see items
     */
    protected function isChildActive($items, &$active)
    {
        foreach ($items as $i => $child) {
            if (ArrayHelper::remove($items[$i], 'active', false) || $this->isItemActive($child)) {
                Html::addCssClass($items[$i]['options'], 'active');
                if ($this->activateParents) {
                    $active = true;
                }
            }
        }
        return $items;
    }

    /**
     * Checks whether a menu item is active.
     * This is done by checking if [[route]] and [[params]] match that specified in the `url` option of the menu item.
     * When the `url` option of a menu item is specified in terms of an array, its first element is treated
     * as the route for the item and the rest of the elements are the associated parameters.
     * Only when its route and parameters match [[route]] and [[params]], respectively, will a menu item
     * be considered active.
     * @param array $item the menu item to be checked
     * @return boolean whether the menu item is active
     */
    protected function isItemActive($item)
    {
        if (isset($item['url']) && is_array($item['url']) && isset($item['url'][0])) {
            $route = $item['url'][0];
            if ($route[0] !== '/' && Yii::$app->controller) {
                $route = Yii::$app->controller->module->getUniqueId() . '/' . $route;
            }
            if (ltrim($route, '/') !== $this->route) {
                return false;
            }
            unset($item['url']['#']);
            if (count($item['url']) > 1) {
                $params = $item['url'];
                unset($params[0]);
                foreach ($params as $name => $value) {
                    if ($value !== null && (!isset($this->params[$name]) || $this->params[$name] != $value)) {
                        return false;
                    }
                }
            }

            return true;
        }

        return false;
    }
}
