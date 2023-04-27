export default function curator({
    statePath,
    types,
    initialSelection = null
}) {
    return {
        statePath,
        types,
        selected: null,
        files: [],
        nextPageUrl: null,
        isFetching: false,
        async init() {
            await this.getFiles('/curator/media', initialSelection?.id);
            const observer = new IntersectionObserver(
                ([e]) => {
                    if (e.isIntersecting) {
                        this.loadMoreFiles();
                    }
                },
                {
                    rootMargin: '0px',
                    threshold: [0],
                }
            );
            observer.observe(this.$refs.loadMore);
            if (initialSelection) {
                this.setSelected(initialSelection.id)
            }
        },
        getFiles: async function (url = '/curator/media', selected = null) {
            if (selected) {
                let indicator = url.includes('?') ? '&' : '?';
                url = url + indicator + 'media_id=' + selected;
            }
            this.isFetching = true;
            const response = await fetch(url);
            const result = await response.json();
            this.files = this.files ? this.files.concat(result.data) : result.data;
            this.nextPageUrl = result.next_page_url;
            this.isFetching = false;
        },
        loadMoreFiles: async function () {
            if (this.nextPageUrl) {
                this.isFetching = true;
                await this.getFiles(this.nextPageUrl, this.selected?.id);
                this.isFetching = false;
            }
        },
        searchFiles: async function (event) {
            this.isFetching = true;
            const response = await fetch('/curator/media/search?q=' + event.target.value);
            const result = await response.json();
            this.files = result.data;
            this.isFetching = false;
        },
        addNewFile: function (media = null) {
            if (media) {
                this.files = [...media, ...this.files];
                this.$nextTick(() => {
                    this.setSelected(media[0].id);
                })
            }
        },
        removeFile: function (media = null) {
            if (media) {
                this.files = this.files.filter((obj) => obj.id !== media.id);
                this.selected = null;
            }
        },
        setSelected: function (mediaId = null) {
            if (!mediaId || (this.selected && this.selected.id === mediaId)) {
                this.selected = null;
            } else {
                this.selected = this.files.find(obj => obj.id === mediaId);
            }

            this.$wire.setCurrentFile(this.selected);
        },
    };
}