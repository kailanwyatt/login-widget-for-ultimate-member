/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';
import { SelectControl, PanelBody } from '@wordpress/components';
import { InspectorControls } from '@wordpress/block-editor';
import { useState, useRef, useEffect } from '@wordpress/element';
/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import { useBlockProps } from '@wordpress/block-editor';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @return {Element} Element to render.
 */
		
export default function Edit( { clientId, attributes, setAttributes }) {
	let defaultValue = um_login_widget_admin.member_forms.length > 0 ? um_login_widget_admin.member_forms[0].value : 0;
	const { form_id } = attributes;
	const divRef = useRef(null);

	useEffect(() => {
        const setFormID = (value) => {
            setAttributes({ form_id: value });
            var xhr = new XMLHttpRequest();
            xhr.open('POST', ajaxurl, true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        if (divRef.current) {
                            divRef.current.innerHTML = xhr.responseText;
                        }
                    } else {
                        console.log(xhr.statusText);
                    }
                }
            };
            var data = 'action=um_load_login_form&form_id=' + form_id;
            xhr.send(data);
        };

        // Update the block content with the AJAX response
        setFormID(form_id);
    }, [clientId, form_id, setAttributes]);

	return (
		<div  { ...useBlockProps() }>
			<InspectorControls>
				<PanelBody title={ __( 'UM Login Settings', 'login-widget-for-ultimate-member' ) }>
					<SelectControl
						label={ __( 'Login Form', 'login-widget-for-ultimate-member' ) }
						value={ form_id }
						onChange={(value) => setAttributes({ form_id: value })}
						options={ um_login_widget_admin.member_forms }
					/>
				</PanelBody>
			</InspectorControls>
			<div ref={divRef}>
				
			</div>
		</div>
	);
}
