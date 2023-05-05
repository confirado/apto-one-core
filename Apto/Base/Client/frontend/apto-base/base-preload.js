import { register } from 'app/apto-loader';

const Preload = [{
    query: 'FindContentSnippetTree',
    arguments: [true],
    fulfilled: function (response) {
        let snippets = {};

        if (null !== response.data.result) {
            snippets = response.data.result;
        }

        register('constants', [
            ['APTO_CONTENT_SNIPPETS', snippets]
        ]);
    },
    rejected: function (error) {
        register('constants', [
            ['APTO_CONTENT_SNIPPETS', {}]
        ]);
    }
}];
export default Preload;