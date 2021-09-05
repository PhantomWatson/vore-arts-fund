<?php
return [
    'error' => '<div class="alert alert-danger">{{content}}</div>',
    'inputContainer' => '<div class="form-group {{type}}{{required}}">{{content}}</div>',
    'inputWithFootnoteContainer' => '<div class="form-group {{type}}{{required}}">{{content}}<p class="footnote">{{footnote}}<p></div>',
    'inputContainerError' => '<div class="form-group has-error has-feedback {{type}}{{required}}">{{content}}{{error}}</div>',
    'inputWithFootnoteContainerError' => '<div class="form-group has-error has-feedback {{type}}{{required}}">{{content}}{{error}}<p class="footnote">{{footnote}}<p></div>',
    'select' => '<select class="form-control" name="{{name}}"{{attrs}}>{{content}}</select>',
    'selectMultiple' =>
        '<select class="form-control" name="{{name}}[]" multiple="multiple"{{attrs}}>{{content}}</select>',
    'input' => '<input class="form-control" type="{{type}}" name="{{name}}"{{attrs}}>',
    'textarea' => '{{label}}<textarea class="form-control" name="{{name}}"{{attrs}}>{{value}}</textarea>',
    'numberContainer' => '<div class="form-group {{type}}{{required}}"><div class="input-group mb-2 mr-sm-2"><div class="input-group-prepend"><div class="input-group-text">{{prefix}}</div></div>{{content}}</div></div>',
    'file' => '
        <div class="custom-file">
            <input type="file" class="custom-file-input" id="customFile" name="{{name}}" {{attrs}} />
        </div>
    ',
    //<label class="custom-file-label" for="customFile">Choose file</label>
    'submitContainer' => '{{content}}',

    /* defaults
    'button' => '<button{{attrs}}>{{text}}</button>',
    'checkbox' => '<input type="checkbox" name="{{name}}" value="{{value}}"{{attrs}}>',
    'checkboxFormGroup' => '{{label}}',
    'checkboxWrapper' => '<div class="checkbox">{{label}}</div>',
    'dateWidget' => '{{year}}{{month}}{{day}}{{hour}}{{minute}}{{second}}{{meridian}}',
    'error' => '<div class="error-message">{{content}}</div>',
    'errorList' => '<ul>{{content}}</ul>',
    'errorItem' => '<li>{{text}}</li>',
    'file' => '<input type="file" name="{{name}}"{{attrs}}>',
    'fieldset' => '<fieldset{{attrs}}>{{content}}</fieldset>',
    'formStart' => '<form{{attrs}}>',
    'formEnd' => '</form>',
    'formGroup' => '{{label}}{{input}}',
    'hiddenBlock' => '<div style="display:none;">{{content}}</div>',
    'input' => '<input type="{{type}}" name="{{name}}"{{attrs}}>',
    'inputSubmit' => '<input type="{{type}}"{{attrs}}>',
    'inputContainer' => '<div class="input {{type}}{{required}}">{{content}}</div>',
    'inputContainerError' => '<div class="input {{type}}{{required}} error">{{content}}{{error}}</div>',
    'label' => '<label{{attrs}}>{{text}}</label>',
    'nestingLabel' => '{{hidden}}<label{{attrs}}>{{input}}{{text}}</label>',
    'legend' => '<legend>{{text}}</legend>',
    'option' => '<option value="{{value}}"{{attrs}}>{{text}}</option>',
    'optgroup' => '<optgroup label="{{label}}"{{attrs}}>{{content}}</optgroup>',
    'select' => '<select name="{{name}}"{{attrs}}>{{content}}</select>',
    'selectMultiple' => '<select name="{{name}}[]" multiple="multiple"{{attrs}}>{{content}}</select>',
    'radio' => '<input type="radio" name="{{name}}" value="{{value}}"{{attrs}}>',
    'radioWrapper' => '{{label}}',
    'textarea' => '<textarea name="{{name}}"{{attrs}}>{{value}}</textarea>',
    'submitContainer' => '<div class="submit">{{content}}</div>',
     */
];
