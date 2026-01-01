/**
 * @license Copyright (c) 2003-2015, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
    // Define changes to default configuration here.
    // For complete reference see:
    // http://docs.ckeditor.com/#!/api/CKEDITOR.config

    // The toolbar groups arrangement, optimized for two toolbar rows.
    config.toolbarGroups = [
        { name: 'clipboard',   groups: [ 'clipboard', 'undo' ] },
        { name: 'editing',     groups: [ 'find', 'selection', 'spellchecker' ] },
        { name: 'links' },
        { name: 'insert' },
        { name: 'forms' },
        { name: 'tools' },
        { name: 'document',	   groups: [ 'mode', 'document', 'doctools' ] },
        { name: 'others' },
        '/',
        { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
        { name: 'paragraph',   groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ] },
        { name: 'styles' },
        { name: 'colors' },
        { name: 'about' }
    ];

    // Remove some buttons provided by the standard plugins, which are
    // not needed in the Standard(s) toolbar.
    config.removeButtons = 'Underline,Subscript,Superscript';

    // Set the most common block elements.
    config.format_tags = 'p;h1;h2;h3;pre';

    // Simplify the dialog windows.
    config.removeDialogTabs = 'image:advanced;link:advanced';

    config.fontSize_sizes = '8/8px;' +
        '9/9px;' +
        '10/10px;' +
        '11/11px;' +
        '12/12px;' +
        '13/13px;' +
        '14/14px;' +
        '15/15px;' +
        '16/16px;' +
        '18/18px;' +
        '19/19px;' +
        '20/20px;' +
        '22/22px;' +
        '24/24px;' +
        '26/26px;' +
        '28/28px;' +
        '30/30px;' +
        '32/32px;' +
        '48/48px;' +
        '64/64px;' +
        '72/72px;';
    // Set default font style and font size
    config.font_defaultLabel = 'Times New Roman';
    config.fontSize_defaultLabel = '13px';
};
