window.$ = jQuery;

window.CMREG_Profile_Fields = {};


window.CMREG_Profile_Fields.initialize = function(selector, data, roles) {
	
	var formBuilder = $(selector).formBuilder({
		dataType: 'json',
		formData: data,
		disableFields: ['autocomplete', 'header', 'hidden', 'paragraph', 'button', 'file'],
		controlOrder: ['text', 'textarea', 'number', 'select', 'radio-group', 'checkbox-group', 'date'],
		showActionButtons: false,
		editOnAdd: true,
		fieldRemoveWarn: true,
		roles: roles,
		i18n: {
//			locale: 'en-US',
//			'location': '/wp-content/plugins/registration/asset/vendors/form-builder/',
//			'extension': '.lang',
			preloaded: {'en-US': CMREG_Profile_Fields_i18n}
		}
	});
	
	$('#cmreg-profile-fields-form').submit(function(ev) {
//		ev.preventDefault();
		var form = $('#cmreg-profile-fields-form');
		var fields = formBuilder.actions.getData();
		
		// Update roles access
		for (var i=0; i<fields.length; i++) {
			var roles = [];
			var wrap = $('#cmreg-form-wrap .stage-wrap > ul > li').get(i);
			var checkboxes = $('.available-roles input[type=checkbox]:checked', wrap);
			for (var j=0; j<checkboxes.length; j++) {
				roles.push(checkboxes[j].value);
			}
//			console.log(roles);
			fields[i].roles = roles;
		}
		
		form.find('textarea').val(JSON.stringify(fields));
//		form.submit();
		
	});
	
};


var CMREG_Profile_Fields_i18n = {
          addOption: 'Add Option +',
          allFieldsRemoved: 'All fields were removed.',
          allowMultipleFiles: 'Allow users to upload multiple files',
          autocomplete: 'Autocomplete',
          button: 'Button',
          cannotBeEmpty: 'This field cannot be empty',
          checkboxGroup: 'Checkbox Group',
          checkbox: 'Checkbox',
          checkboxes: 'Checkboxes',
          className: 'CSS class',
          clearAllMessage: 'Are you sure you want to clear all fields?',
          clear: 'Clear',
          close: 'Close',
          content: 'Content',
          copy: 'Copy To Clipboard',
          copyButton: '&#43;',
          copyButtonTooltip: 'Copy',
          dateField: 'Date Field',
          description: 'Tooltip text',
          descriptionField: 'Description',
          devMode: 'Developer Mode',
          editNames: 'Edit Names',
          editorTitle: 'Form Elements',
          editXML: 'Edit XML',
          enableOther: 'Enable &quot;Other&quot;',
          enableOtherMsg: 'Let users to enter an unlisted option',
          fieldNonEditable: 'This field cannot be edited.',
          fieldRemoveWarning: 'Are you sure you want to remove this field?',
          fileUpload: 'File Upload',
          formUpdated: 'Form Updated',
          getStarted: 'Drag a field from the right to this area',
          header: 'Header',
          hide: 'Edit',
          hidden: 'Hidden Input',
          inline: 'Inline',
          inlineDesc: 'Display {type} inline',
          label: 'Change label:',
          labelEmpty: 'Field Label cannot be empty',
          limitRole: 'Limit access to one or more of the following roles',
          mandatory: 'Mandatory',
          maxlength: 'Max Length',
          minOptionMessage: 'This field requires a minimum of 2 options',
          multipleFiles: 'Multiple Files',
          name: '<span title="Store the value entered by user in a wp_usermeta table under the following key">User meta key</span>',
          no: 'No',
          noFieldsToClear: 'There are no fields to clear',
          number: 'Number',
          off: 'Off',
          on: 'On',
          option: 'Option',
          options: 'Options',
          optional: 'optional',
          optionLabelPlaceholder: 'Label',
          optionValuePlaceholder: 'Value',
          optionEmpty: 'Option value required',
          other: 'Other',
          paragraph: 'Paragraph',
          placeholder: 'Placeholder',
          'placeholder.value': 'Value',
          'placeholder.label': 'Label',
          'placeholder.text': '',
          'placeholder.textarea': '',
          'placeholder.email': 'Enter you email',
          'placeholder.placeholder': '',
          'placeholder.className': 'space separated classes',
          'placeholder.password': 'Enter your password',
          preview: 'Preview',
          radioGroup: 'Radio Group',
          radio: 'Radio',
          removeMessage: 'Remove Element',
          removeOption: 'Remove Option',
          remove: '&#215;',
          required: 'Required',
          richText: 'Rich Text Editor',
          roles: 'Restrict to roles',
          rows: 'Rows',
          save: 'Save',
          selectOptions: 'Options',
          select: 'Select',
          selectColor: 'Select Color',
          selectionsMessage: 'Allow Multiple Selections',
          size: 'Size',
          'size.xs': 'Extra Small',
          'size.sm': 'Small',
          'size.m': 'Default',
          'size.lg': 'Large',
          style: 'Style',
          styles: {
            btn: {
              'default': 'Default',
              danger: 'Danger',
              info: 'Info',
              primary: 'Primary',
              success: 'Success',
              warning: 'Warning'
            }
          },
          subtype: 'Type',
          text: 'Text Input',
          textArea: 'Text Area',
          toggle: 'Toggle',
          warning: 'Warning!',
          value: 'Default value',
          viewJSON: '{  }',
          viewXML: '&lt;/&gt;',
          yes: 'Yes'
        };