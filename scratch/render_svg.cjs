const fs = require('fs');
const opentype = require('opentype.js');

const buffer = fs.readFileSync('../revizy/public/tracing-studio/KGPrimaryPenmanshipAlt.ttf');
const font = opentype.parse(buffer);
const path = font.getPath('a', 300, 300, 350);

const svg = `
<svg width="600" height="600" xmlns="http://www.w3.org/2000/svg">
  <path d="${path.toPathData()}" fill="black" />
</svg>
`;
fs.writeFileSync('scratch/test.svg', svg);
