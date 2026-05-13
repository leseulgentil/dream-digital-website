import fs from 'fs/promises';
import path from 'path';
import { glob } from 'glob';
import { getIconsCSS } from '@iconify/utils';

const iconSourceGlobs = [
  'config/dream-digital/**/*.php',
  'resources/assets/js/dd-*.js',
  'resources/assets/js/front*.js',
  'resources/menu/**/*.json',
  'resources/views/admin/**/*.blade.php',
  'resources/views/content/front-pages/blog-*.blade.php',
  'resources/views/content/front-pages/cms-preview.blade.php',
  'resources/views/content/front-pages/landing-page.blade.php',
  'resources/views/content/front-pages/legal-page.blade.php',
  'resources/views/content/front-pages/marketing-page.blade.php',
  'resources/views/front/**/*.blade.php',
  'resources/views/layouts/sections/footer/**/*.blade.php',
  'resources/views/layouts/sections/navbar/**/*.blade.php'
];

const fallbackIcons = {
  bx: [
    'book-content',
    'broadcast',
    'conversation',
    'copy',
    'desktop',
    'file',
    'home-smile',
    'link-external',
    'menu',
    'moon',
    'news',
    'plus',
    'purchase-tag-alt',
    'right-arrow-alt',
    'save',
    'show',
    'sun',
    'trash',
    'user',
    'world'
  ],
  bxl: [],
  bxs: []
};

async function collectUsedIcons() {
  const used = new Map([
    ['bx', new Set(fallbackIcons.bx)],
    ['bxl', new Set(fallbackIcons.bxl)],
    ['bxs', new Set(fallbackIcons.bxs)]
  ]);

  const files = glob.sync(iconSourceGlobs, { nodir: true, windowsPathsNoEscape: true });
  const iconRegex = /\b(bx|bxl|bxs)-([a-z0-9-]+)\b/g;

  for (const file of files) {
    const content = await fs.readFile(path.resolve(process.cwd(), file), 'utf-8');
    for (const match of content.matchAll(iconRegex)) {
      used.get(match[1])?.add(match[2]);
    }
  }

  return used;
}

export default function iconifyPlugin() {
  return {
    name: 'vite-iconify-plugin',
    apply: 'build',

    async buildStart() {
      console.log('Generating Iconify CSS file...');

      try {
        const usedIcons = await collectUsedIcons();
        const iconSetPaths = [
          path.resolve(process.cwd(), 'node_modules/@iconify/json/json/bx.json'),
          path.resolve(process.cwd(), 'node_modules/@iconify/json/json/bxl.json'),
          path.resolve(process.cwd(), 'node_modules/@iconify/json/json/bxs.json')
        ];

        const iconSets = await Promise.all(
          iconSetPaths.map(async filePath => {
            const data = await fs.readFile(filePath, 'utf-8');
            return JSON.parse(data);
          })
        );

        const allIcons = iconSets
          .map(iconSet => {
            const names = [...(usedIcons.get(iconSet.prefix) ?? new Set())].filter(name => iconSet.icons[name]);

            if (!names.length) return '';

            return getIconsCSS(iconSet, names, {
              iconSelector: '.{prefix}-{name}',
              commonSelector: '.bx',
              format: 'expanded'
            });
          })
          .join('\n');

        const outputPath = path.resolve(process.cwd(), 'resources/assets/vendor/fonts/iconify/iconify.css');
        const dir = path.dirname(outputPath);
        await fs.mkdir(dir, { recursive: true });
        await fs.writeFile(outputPath, `${allIcons.trimEnd()}\n`, 'utf8');

        const iconCount = [...usedIcons.values()].reduce((total, icons) => total + icons.size, 0);
        console.log(`Iconify CSS generated at: ${outputPath} (${iconCount} icons)`);

        const additionalFiles = [
          {
            name: 'fontawesome',
            filesPath: path.resolve(process.cwd(), 'node_modules/@fortawesome/fontawesome-free/webfonts'),
            destPath: path.resolve(process.cwd(), 'resources/assets/vendor/fonts/fontawesome')
          },
          {
            name: 'flags',
            filesPath: path.resolve(process.cwd(), 'node_modules/flag-icons/flags'),
            destPath: path.resolve(process.cwd(), 'resources/assets/vendor/fonts/flags')
          }
        ];

        for (const file of additionalFiles) {
          await fs.mkdir(file.destPath, { recursive: true });
          const items = await fs.readdir(file.filesPath, { withFileTypes: true });
          for (const item of items) {
            const srcPath = path.join(file.filesPath, item.name);
            const destPath = path.join(file.destPath, item.name);
            if (item.isDirectory()) {
              await fs.mkdir(destPath, { recursive: true });
              const subItems = await fs.readdir(srcPath);
              for (const subItem of subItems) {
                await fs.copyFile(path.join(srcPath, subItem), path.join(destPath, subItem));
              }
            } else {
              await fs.copyFile(srcPath, destPath);
            }
          }
        }
      } catch (error) {
        console.error('Error generating Iconify CSS or copying additional files:', error);
      }
    }
  };
}
