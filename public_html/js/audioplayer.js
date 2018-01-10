soundManager.setup({
    url: '/path/to/swf-directory/',

    onready: function () {
        // SM2 has loaded, API ready to use e.g., createSound() etc.
    },

    ontimeout: function () {
        // Uh-oh. No HTML5 support, SWF missing, Flash blocked or other issue
    }

});