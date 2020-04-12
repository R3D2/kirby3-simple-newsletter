<template>
    <div>
        <k-button 
            icon="email"
            theme="button"
            type="submit"
            v-on:click="$refs.test.open()"
        >
        Envoyer un Test    
        </k-button>
        <k-dialog
            ref="test"
            button="Envoyer le test"
            theme="positive"
            icon="check"
            @submit="test"
        >
            <k-text>
                Êtes vous sûr de vouloir envoyer la Newsletter de test ?
            </k-text>
        </k-dialog>
        <k-button 
            icon="user"
            theme="button"
            class="k-send-button"
            type="submit"
            link="/pages/abonnes"
            v-on:click="subscriberView"
        >
        Voir la liste des abonnés  
        </k-button>
        <k-button 
            icon="check"
            theme="button"
            class="k-send-button"
            type="submit"
            v-on:click="$refs.send_newsletter.open()"
        >
        Envoyer la Newsletter  
        </k-button>
        <k-dialog
            ref="send_newsletter"
            button="Envoyer la Newsletter"
            theme="positive"
            icon="check"
            size="medium"
            @submit="send"
        >
            <k-text>
                Êtes-vous sûr de vouloir envoyer la Newsletter ?
            </k-text>
        </k-dialog>
    </div>
</template>

<script>
export default {
    props: {
        data: String,
        pageURI: String,
        id: String,
        status: Boolean
    },
    methods: {
        test() {
            var self = this;
            let url = 'newsletter/test/' + encodeURI(this.pageURI);
            this.$api.get(url);
            this.$refs.test.success("Test Envoyé !");
        },
        send() {
            var self = this;
            let url = 'newsletter/send/' + encodeURI(this.pageURI);
            this.$api.get(url);
            this.$refs.send_newsletter.success("Newsletter Envoyé !");
        },
    }
}
</script>

<style scoped>
.k-send-button {
    margin-left: 20px;
}
</style>