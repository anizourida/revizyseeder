const opentype = require('opentype.js');
opentype.load('../revizy/public/tracing-studio/KGPrimaryPenmanshipAlt.ttf', (err, font) => {
    if(err) { console.error(err); return; }
    const path = font.getPath('a', 0, 0, 350);
    console.log("Bounding box:", path.getBoundingBox());
    console.log("Path commands:", path.commands.slice(0, 3));
});
