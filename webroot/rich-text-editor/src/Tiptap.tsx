// src/Tiptap.tsx
import {EditorContent, useEditor} from '@tiptap/react'
import {StarterKit} from "@tiptap/starter-kit";
import Link from '@tiptap/extension-link';
import {useCallback} from "react";

const getLinkConfiguration = () => Link.configure({
    openOnClick: false,
    autolink: true,
    defaultProtocol: 'https',
    protocols: ['http', 'https'],
    isAllowedUri: (url, ctx) => {
        try {
            // construct URL
            const parsedUrl = url.includes(':') ? new URL(url) : new URL(`${ctx.defaultProtocol}://${url}`);

            // use default validation
            if (!ctx.defaultValidate(parsedUrl.href)) {
                return false;
            }

            // disallowed protocols
            const disallowedProtocols = ['ftp', 'file'];
            const protocol = parsedUrl.protocol.replace(':', '');

            if (disallowedProtocols.includes(protocol)) {
                return false;
            }

            // only allow protocols specified in ctx.protocols
            const allowedProtocols = ctx.protocols.map(p => (typeof p === 'string' ? p : p.scheme));

            if (!allowedProtocols.includes(protocol)) {
                return false;
            }

            // disallowed domains
            const disallowedDomains = [] as string[];
            const domain = parsedUrl.hostname;

            if (disallowedDomains.includes(domain)) {
                return false;
            }

            // all checks have passed
            return true;
        } catch {
            return false;
        }
    },
    shouldAutoLink: url => {
        try {
            // construct URL
            const parsedUrl = url.includes(':') ? new URL(url) : new URL(`https://${url}`);

            // only auto-link if the domain is not in the disallowed list
            const disallowedDomains = [] as string[];
            const domain = parsedUrl.hostname;

            return !disallowedDomains.includes(domain);
        } catch {
            return false
        }
    },
});

const extensions = [
    StarterKit,
    getLinkConfiguration()
];

function getUnescapedInnerHTML(html: string): string {
    const tempElement = document.createElement('div');
    tempElement.innerHTML = html;
    return tempElement.textContent ?? '';
}

const target = document.querySelector('[data-rte-target]') as HTMLElement;
const content = target
    ? getUnescapedInnerHTML(target.innerHTML)
    : '<p></p>';
// Hide the original <textarea> that's being updated
if (target) {
    target.style.display = 'none';
}

const Tiptap = () => {
    const editor = useEditor({
        extensions,
        content,
    });
    if (!editor) {
        return null;
    }
    editor.on('update', (editor) => {
        // Not sure why it's editor.editor. This contradicts the docs (https://tiptap.dev/docs/editor/api/events#bind-event-listeners)
        target.innerHTML = editor.editor.getHTML();
    });

    const setLink = useCallback(() => {
        const previousUrl = editor.getAttributes('link').href;
        const url = window.prompt('URL', previousUrl);

        // cancelled
        if (url === null) {
            return;
        }

        // empty
        if (url === '') {
            editor.chain().focus().extendMarkRange('link').unsetLink().run();
            return;
        }

        // update link
        try {
            editor.chain().focus().extendMarkRange('link').setLink({ href: url }).run();
        } catch (e) {
            alert((e as Error).message);
        }
    }, [editor]);

    const btnClasses = 'btn btn-sm btn-secondary ';

    return (
        <>
            <div className="button-group">
                <button
                    type="button"
                    onClick={() => editor!.chain().focus().toggleBold().run()}
                    className={btnClasses + (editor.isActive('bold') ? 'is-active' : '')}
                >
                    Bold
                </button>
                <button
                    type="button"
                    onClick={() => editor!.chain().focus().toggleItalic().run()}
                    className={btnClasses + (editor.isActive('italic') ? 'is-active' : '')}
                >
                    Italic
                </button>
                <button
                    type="button"
                    onClick={setLink}
                    className={btnClasses + (editor.isActive('link') ? 'is-active' : '')}
                >
                    Link
                </button>
                <button
                    type="button"
                    onClick={() => editor!.chain().focus().unsetLink().run()}
                    disabled={!editor.isActive('link')}
                    className={btnClasses + (editor.isActive('link') ? 'is-active' : '')}
                >
                    Unlink
                </button>
            </div>
            <EditorContent editor={editor} className="form-control" />
        </>
    )
}

export default Tiptap
