import { c as createComponent, r as renderComponent, a as renderTemplate, d as createAstro, m as maybeRenderHead, b as addAttribute } from '../../chunks/astro/server_C3wNZAeM.mjs';
import 'piccolore';
import 'html-escaper';
import { g as getProductsByCategory, $ as $$BaseLayout, C as CATEGORIES } from '../../chunks/BaseLayout_BlgaMPsg.mjs';
import { $ as $$ProductCard } from '../../chunks/ProductCard_B5E4BfSl.mjs';
export { renderers } from '../../renderers.mjs';

const $$Astro = createAstro();
function getStaticPaths() {
  return CATEGORIES.map((category) => ({
    params: { slug: category.slug },
    props: { category }
  }));
}
const $$slug = createComponent(($$result, $$props, $$slots) => {
  const Astro2 = $$result.createAstro($$Astro, $$props, $$slots);
  Astro2.self = $$slug;
  const { category } = Astro2.props;
  const products = getProductsByCategory(category.slug);
  return renderTemplate`${renderComponent($$result, "BaseLayout", $$BaseLayout, { "title": `${category.name} \u2014 El Yap\u0131m\u0131 \xDCr\xFCnler` }, { "default": ($$result2) => renderTemplate` ${maybeRenderHead()}<div class="container" style="padding: 3rem 1.5rem;"> <div style="margin-bottom: 2.5rem; display: flex; align-items: center; gap: 1.5rem;"> <img${addAttribute(category.image_url, "src")}${addAttribute(category.name, "alt")} style="width: 90px; height: 90px; border-radius: var(--radius-md); object-fit: cover;"> <div> <h1 style="font-size: 2.2rem; margin-bottom: 0.3rem;">${category.icon} ${category.name}</h1> <p style="color: #64748b; font-size: 1rem;">${category.description}</p> </div> </div> ${products.length === 0 ? renderTemplate`<div class="card" style="padding: 3rem; text-align: center;"> <p>Bu kategoride henüz ürün bulunmuyor.</p> </div>` : renderTemplate`<div class="grid-4"> ${products.map((p) => renderTemplate`${renderComponent($$result2, "ProductCard", $$ProductCard, { "product": p })}`)} </div>`} </div> ` })}`;
}, "E:/PROJECT/web/butikcarsi/frontend/src/pages/kategori/[slug].astro", void 0);

const $$file = "E:/PROJECT/web/butikcarsi/frontend/src/pages/kategori/[slug].astro";
const $$url = "/kategori/[slug]";

const _page = /*#__PURE__*/Object.freeze(/*#__PURE__*/Object.defineProperty({
    __proto__: null,
    default: $$slug,
    file: $$file,
    getStaticPaths,
    url: $$url
}, Symbol.toStringTag, { value: 'Module' }));

const page = () => _page;

export { page };
