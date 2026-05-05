const fs = require('fs');
const opentype = require('opentype.js');
const buffer = fs.readFileSync('../revizy/public/tracing-studio/KGPrimaryPenmanshipAlt.ttf');
const font = opentype.parse(buffer);
const path = font.getPath('a', 0, 0, 350);
console.log("Bounding box:", path.getBoundingBox());
console.log("Path commands:", path.commands.slice(0, 3));
