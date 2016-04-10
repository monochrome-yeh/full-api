/*!
 * FileInput Chinese Translations
 *
 * This file must be loaded after 'fileinput.js'. Patterns in braces '{}', or
 * any HTML markup tags in the messages must not be converted or translated.
 *
 * @see http://github.com/kartik-v/bootstrap-fileinput
 * @author kangqf <kangqingfei@gmail.com>
 *
 * NOTE: this file must be saved in UTF-8 encoding.
 */
(function ($) {
    "use strict";

    $.fn.fileinputLocales['zh-TW'] = {
        fileSingle: '文件',
        filePlural: '多個文件',
        browseLabel: '選擇 &hellip;',
        removeLabel: '移除',
        removeTitle: '清除選中文件',
        cancelLabel: '取消',
        cancelTitle: '取消進行中的上傳',
        uploadLabel: '上傳',
        uploadTitle: '上傳選中文件',
        msgSizeTooLarge: '文件 "{name}" (<b>{size} KB</b>) 超過了允許大小 <b>{maxSize} KB</b>. 請重新上傳!',
        msgFilesTooLess: '你必須選擇最少 <b>{n}</b> {files} 來上傳. 請重新上傳!',
        msgFilesTooMany: '選擇的上傳文件個數 <b>({n})</b> 超出最大文件的限制個數 <b>{m}</b>. 請重新上傳!',
        msgFileNotFound: '文件 "{name}" 未找到!',
        msgFileSecured: '安全限制，為了防止讀取文件 "{name}".',
        msgFileNotReadable: '文件 "{name}" 不可讀.',
        msgFilePreviewAborted: '取消 "{name}" 的預覽.',
        msgFilePreviewError: '讀取 "{name}" 時出現了一個錯誤.',
        msgInvalidFileType: '不正確的類型 "{name}". 只支持 "{types}" 類型的文件.',
        msgInvalidFileExtension: '不正確的文件擴展名 "{name}". 只支持 "{extensions}" 的文件擴展名.',
        msgValidationError: '文件上傳錯誤',
        msgLoading: '加載第 {index} 文件 共 {files} &hellip;',
        msgProgress: '加載第 {index} 文件 共 {files} - {name} - {percent}% 完成.',
        msgSelected: '{n} {files} 選中',
        msgFoldersNotAllowed: '只支持拖拽文件! 跳過 {n} 拖拽的文件夾.',
        dropZoneTitle: '拖拽文件到這里 &hellip;',
        slugCallback: function(text) {
            return text ? text.split(/(\\|\/)/g).pop().replace(/[^\w\u4e00-\u9fa5\-.\\\/ ]+/g, '') : '';
        }
    };
})(window.jQuery);