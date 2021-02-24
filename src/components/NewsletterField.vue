<template>
     <div>
        <k-button 
            icon="email"
            theme="button"
            type="submit"
            v-on:click="$refs.dialog_test.open()"
        >
        {{ $t('scardoso.newsletter.sendTestMail') }}
        </k-button>
        <k-dialog
            ref="dialog_test"
            button="Envoyer le test"
            theme="positive"
            icon="check"
            @submit="send(test=true)"
        >
            <k-text>
                {{ $t('scardoso.newsletter.t.confirmSendTestNewsletter') }}
            </k-text>
        </k-dialog>
        <k-button 
            icon="user"
            theme="button"
            class="k-send-button"
            type="submit"
            v-bind:link="subscriberLink"
        >
        {{ $t('scardoso.newsletter.viewSubscribers') }}
        </k-button>
        <k-button 
            icon="check"
            theme="positive"
            class="k-send-button"
            type="submit"
            v-on:click="$refs.dialog_send.open()"
        >
        {{ $t('scardoso.newsletter.sendNewsletter') }}  
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
                {{ $t('scardoso.newsletter.t.confirmSendNewsletter') }}
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
            this.$api.get(url)
            .then(response => {
                console.log(response);
                if (test) {
                    this.$refs.dialog_test.success(response.message);
                    window.setTimeout(() => location.reload(), 2000);
                } else {
                    this.$refs.dialog_send.success(response.message);
                    window.setTimeout(() => location.reload(), 2000);
                }
            })
            .catch(error => {
                this.$refs.dialog_test.error(error.message);
            })
        },
    }
}
</script>

<style scoped>
.k-send-button {
    margin-left: 20px;
}
</style>