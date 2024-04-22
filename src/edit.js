/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';
import { SelectControl, PanelBody, TextControl } from '@wordpress/components';
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
	const { form_id, show_avatar, show_account, show_edit_profile, show_profile_url, show_profile_tabs, show_logout, title } = attributes;

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
            var data = 'action=um_load_login_form&form_id=' + form_id + '&show_account=' + show_account + '&show_edit_profile=' + show_edit_profile + '&show_profile_url=' + show_profile_url + '&show_profile_tabs=' + show_profile_tabs + '&show_avatar=' + show_avatar + '&show_logout=' + show_logout;
            xhr.send(data);
        };

        // Update the block content with the AJAX response
        setFormID(form_id);
    }, [clientId, form_id, show_account, show_avatar, show_edit_profile, show_logout, show_profile_url, show_profile_tabs, setAttributes]);

	return (
		<div { ...useBlockProps() }>
			<InspectorControls>
				<PanelBody title={ __( 'UM Login Settings', 'login-widget-for-ultimate-member' ) }>
                    <TextControl
                        label={ __( 'Title', 'login-widget-for-ultimate-member' ) }
                        value={ title }
                        onChange={(value) => setAttributes({ title: value })}
                    />
					<SelectControl
						label={ __( 'Login Form', 'login-widget-for-ultimate-member' ) }
						value={ form_id }
						onChange={(value) => setAttributes({ form_id: value })}
						options={ um_login_widget_admin.member_forms }
					/>
                    <SelectControl
                        label={ __( 'Show Avatar', 'login-widget-for-ultimate-member' ) }
                        value={ show_avatar }
                        onChange={(value) => setAttributes({ show_avatar: value })}
                        options={ [
                            { label: __( 'Yes', 'login-widget-for-ultimate-member' ), value: 1 },
                            { label: __( 'No', 'login-widget-for-ultimate-member' ), value: 0 },
                        ] }
                    />
                    <SelectControl
                        label={ __( 'Show Account Edit', 'login-widget-for-ultimate-member' ) }
                        value={ show_account }
                        onChange={(value) => setAttributes({ show_account: value })}
                        options={ [
                            { label: __( 'Yes', 'login-widget-for-ultimate-member' ), value: 1 },
                            { label: __( 'No', 'login-widget-for-ultimate-member' ), value: 0 },
                        ] }
                    />
                    <SelectControl  
                        label={ __( 'Show Logout', 'login-widget-for-ultimate-member' ) }
                        value={ show_logout }
                        onChange={(value) => setAttributes({ show_logout: value })}
                        options={ [
                            { label: __( 'Yes', 'login-widget-for-ultimate-member' ), value: 1 },
                            { label: __( 'No', 'login-widget-for-ultimate-member' ), value: 0 },
                        ] }
                    />
                    <SelectControl
                        label={ __( 'Show Profile Edit', 'login-widget-for-ultimate-member' ) }
                        value={ show_edit_profile }
                        onChange={(value) => setAttributes({ show_edit_profile: value })}
                        options={ [
                            { label: __( 'Yes', 'login-widget-for-ultimate-member' ), value: 1 },
                            { label: __( 'No', 'login-widget-for-ultimate-member' ), value: 0 },
                        ] }
                    />
                    <SelectControl
                        label={ __( 'Show Profile URL', 'login-widget-for-ultimate-member' ) }
                        value={ show_profile_url }
                        onChange={(value) => setAttributes({ show_profile_url: value })}
                        options={ [
                            { label: __( 'Yes', 'login-widget-for-ultimate-member' ), value: 1 },
                            { label: __( 'No', 'login-widget-for-ultimate-member' ), value: 0 },
                        ] }
                    />
                    <SelectControl  
                        label={ __( 'Show Profile Tabs', 'login-widget-for-ultimate-member' ) }
                        value={ show_profile_tabs }
                        onChange={(value) => setAttributes({ show_profile_tabs: value })}
                        options={ [
                            { label: __( 'Yes', 'login-widget-for-ultimate-member' ), value: 1 },
                            { label: __( 'No', 'login-widget-for-ultimate-member' ), value: 0 },
                        ] }
                    />
				</PanelBody>
			</InspectorControls>
			<div ref={divRef}>
				{ __( 'Select a form', 'login-widget-for-ultimate-member' ) }
			</div>
		</div>
	);
}
