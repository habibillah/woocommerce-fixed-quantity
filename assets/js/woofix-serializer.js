(function($){

    $.fn.serializeWofix = function(woofixjs_admin){

        var patterns = {
            validate: /^[a-z_][a-z0-9_]*(?:\[(?:\d*|[a-z0-9_]+)\])*$/i,
            key:      /[a-z0-9_]+|(?=\[\])/gi,
            push:     /^$/,
            fixed:    /^\d+$/,
            named:    /^[a-z0-9_]+$/i
        };

        // private variables
        var data     = {},
            pushes   = {};

        // private API
        function build(base, key, value) {
            base[key] = value;
            return base;
        }

        function makeObject(root, value) {

            var keyArr = root.split(':');
            if (keyArr.length === 2) {
                root = keyArr[0];
                if (keyArr[1] == 'number') {
                    value = parseInt(value);
                }
                if (keyArr[1] == 'woodecimal') {
                    value = window.accounting.unformat(value, woofixjs_admin.decimal_point);
                }
            }

            var keys = root.match(patterns.key), k;

            // nest, nest, ..., nest
            while ((k = keys.pop()) !== undefined) {
                // foo[]
                if (patterns.push.test(k)) {
                    var idx = incrementPush(root.replace(/\[\]$/, ''));
                    value = build([], idx, value);
                }

                // foo[n]
                else if (patterns.fixed.test(k)) {
                    value = build([], k, value);
                }

                // foo; foo[bar]
                else if (patterns.named.test(k)) {
                    value = build({}, k, value);
                }
            }

            return value;
        }

        function incrementPush(key) {
            if (pushes[key] === undefined) {
                pushes[key] = 0;
            }
            return pushes[key]++;
        }

        function addPair(pair) {
            var obj = makeObject(pair.name, pair.value);
            data = $.extend(true, data, obj);
        }

        var pairsArr = this.serializeArray();
        for (var i=0, len=pairsArr.length; i<len; i++) {
            addPair(pairsArr[i]);
        }

        return data;

    };

})(jQuery);


