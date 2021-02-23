<template>
     <div>
        <k-button 
            icon="email"
            theme="button"
            type="submit"
            v-on:click="$refs.dialog_test.open()"
        >
        Envoyer un Test    
        </k-button>
        <k-dialog
            ref="dialog_test"
            button="Envoyer le test"
            theme="positive"
            icon="check"
            @submit="send(test=true)"
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
            v-bind:link="subscriberLink"
        >
        Voir la liste des abonnés  
        </k-button>
        <k-button 
            icon="check"
            theme="button"
            class="k-send-button"
            type="submit"
            v-on:click="$refs.dialog_send.open()"
        >
        Envoyer la Newsletter  
        </k-button>
        <k-dialog
            ref="dialog_send"
            button="Envoyer la Newsletter"
            theme="positive"
            icon="check"
            size="medium"
            @submit="send(test=false)"
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
        status: Boolean,
        subscriberLink: String,
    },
    methods: {
        send(test) {
            let isTest = (test) ? '/0' : '/1';
            let url = 'newsletter/send/' + encodeURI(this.pageURI) + isTest;
            this.$api.get(url);
            if (test) {
                this.$refs.dialog_test.success("Test Envoyé !");
            } else {
                this.$refs.dialog_send.success("Newsletter Envoyé !");
            }
        },
    }
}
</script>

<style scoped>
.k-send-button {
    margin-left: 20px;
}
</style>