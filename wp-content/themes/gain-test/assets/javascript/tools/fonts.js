import FontFaceObserver from 'fontfaceobserver';

export default class FontLoader {
    fonts = [];

    constructor(fontsArray) {
        this.fonts = fontsArray;
    }

    load() {
        const fontsToLoad = [];

        this.fonts.map((font) => {
            const fontObserver =
                font.weight !== undefined
                    ? new FontFaceObserver(font.name, { weight: font.weight }).load(null, 400)
                    : new FontFaceObserver(font.name).load(null, 400);

            fontsToLoad.push(fontObserver);
        });

        Promise.all(fontsToLoad)
            .then(() => {
                // eslint-disable-next-line no-console
                console.log('All fonts have loaded via FontFaceObserver');
                this.saveSession();
            })
            .catch(() => {
                this.removeSession();
            });

        this.setLoadingClass();
    }

    saveSession() {
        sessionStorage.fontsLoaded = true;

        this.setLoadingClass();
    }

    removeSession() {
        sessionStorage.fontsLoaded = false;
    }

    setLoadingClass() {
        if (sessionStorage.fontsLoaded) {
            document.documentElement.classList.add('fonts-loaded');
        }
    }
}
