<?php

namespace HillTech\NovaKeyValueTinyMCE;

use Laravel\Nova\Fields\Expandable;
use Laravel\Nova\Fields\Field;
use Laravel\Nova\Http\Requests\NovaRequest;

class NovaKeyValueTinyMCE extends Field
{
    use Expandable;

    public $showOnIndex = false;

    public $asHtml = true;

    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'Nova-KeyValueTinyMCE';
    /**
     * The label that should be used for the key heading.
     *
     * @var string|null
     */
    public $keyLabel;
    /**
     * The label that should be used for the value heading.
     *
     * @var string|null
     */
    public $valueLabel;
    /**
     * The label that should be used for the "add row" button.
     *
     * @var string|null
     */
    public $actionText;
    /**
     * The callback used to determine if the keys are readonly.
     *
     * @var (callable(\Laravel\Nova\Http\Requests\NovaRequest):(bool))|bool|null
     */
    public $readonlyKeysCallback;
    /**
     * Determine if new rows are able to be added.
     *
     * @var bool
     */
    public $canAddRow = true;
    /**
     * Determine if rows are able to be deleted.
     *
     * @var bool
     */
    public $canDeleteRow = true;

    public function __construct(string $name, $attribute = null, callable $resolveCallback = null)
    {
        parent::__construct($name, $attribute, $resolveCallback);

        $this->withMeta([
            'options' => config('nova-kvw-tinymce.default_options'),
        ]);
    }

    /**
     * The label that should be used for the key table heading.
     *
     * @param string $label
     * @return $this
     */
    public function keyLabel($label)
    {
        $this->keyLabel = $label;

        return $this;
    }

    /**
     * The label that should be used for the value table heading.
     *
     * @param string $label
     * @return $this
     */
    public function valueLabel($label)
    {
        $this->valueLabel = $label;

        return $this;
    }

    /**
     * The label that should be used for the add row button.
     *
     * @param string $label
     * @return $this
     */
    public function actionText($label)
    {
        $this->actionText = $label;

        return $this;
    }

    /**
     * Set the callback used to determine if the keys are readonly.
     *
     * @param (callable(\Laravel\Nova\Http\Requests\NovaRequest):(bool))|bool $callback
     * @return $this
     */
    public function disableEditingKeys($callback = true)
    {
        $this->readonlyKeysCallback = $callback;

        return $this;
    }

    /**
     * Disable adding new rows.
     *
     * @return $this
     */
    public function disableAddingRows()
    {
        $this->canAddRow = false;

        return $this;
    }

    /**
     * Disable deleting rows.
     *
     * @return $this
     */
    public function disableDeletingRows()
    {
        $this->canDeleteRow = false;

        return $this;
    }

    public function asHtml(bool $asHtml = true)
    {
        $this->asHtml = $asHtml;

        return $this;
    }

    /**
     * Allow to pass any existing TinyMCE option to the editor.
     * Consult the TinyMCE documentation [https://github.com/tinymce/tinymce-vue]
     * to view the list of all the available options.
     *
     * @param array $options
     * @return self
     */
    public function options(array $options)
    {
        $currentOptions = $this->meta['options'];

        return $this->withMeta([
            'options' => array_merge($currentOptions, $options),
        ]);
    }

    /**
     * Set the field id html attribute.
     *
     * @return $this
     */
    public function id($id)
    {
        $this->withMeta([
            'id' => $id,
        ]);

        return $this;
    }

    /**
     * Prepare the element for JSON serialization.
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        return array_merge(parent::jsonSerialize(), [
            'keyLabel' => $this->keyLabel ?? __('Key'),
            'valueLabel' => $this->valueLabel ?? __('Value'),
            'actionText' => $this->actionText ?? __('Add row'),
            'readonlyKeys' => $this->readonlyKeys(app(NovaRequest::class)),
            'canAddRow' => $this->canAddRow,
            'canDeleteRow' => $this->canDeleteRow,
            'asHtml' => $this->asHtml,
            'shouldShow' => $this->shouldBeExpanded(),
        ]);

    }

    /**
     * Determine if the keys are readonly.
     *
     * @param \Laravel\Nova\Http\Requests\NovaRequest $request
     * @return bool
     */
    public function readonlyKeys(NovaRequest $request)
    {
        return with($this->readonlyKeysCallback, function ($callback) use ($request) {
            return is_callable($callback) ? call_user_func($callback, $request) : ($callback === true);
        });
    }

    /**
     * Hydrate the given attribute on the model based on the incoming request.
     *
     * @param \Laravel\Nova\Http\Requests\NovaRequest $request
     * @param string $requestAttribute
     * @param object $model
     * @param string $attribute
     * @return void
     */
    protected function fillAttributeFromRequest(NovaRequest $request, $requestAttribute, $model, $attribute)
    {
        if ($request->exists($requestAttribute)) {
            $model->{$attribute} = json_decode($request[$requestAttribute], true);
        }
    }
}
