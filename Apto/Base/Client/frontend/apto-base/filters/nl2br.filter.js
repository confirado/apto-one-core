const nl2brFilterInject = [];
const nl2brFilter = function () {
    function nl2br(str) {
        let breakTag = '<br />';
        return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
    }

    return nl2br;
};
nl2brFilter.$inject = nl2brFilterInject;

export default ['nl2br', nl2brFilter];