import NewsletterField from "./components/NewsletterField";
import NewsletterBodyField from "./components/NewsletterBodyField";

panel.plugin('scardoso/simple-newsletter', {
    fields: {
        newsletter: NewsletterField,
        newsletterBody: NewsletterBodyField
    }
});