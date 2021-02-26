<template>
     <div>
        <k-bar class="k-newsletter-bar">
            <template slot="left">
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
                    submitButton="Send away!"
                    theme="positive"
                    icon="email"
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
                    class="k-send-button"
                    type="submit"
                    v-on:click="$refs.dialog_send.open()"
                >
                {{ $t('scardoso.newsletter.sendNewsletter') }}  
                </k-button>
                <k-dialog
                    ref="dialog_send"
                    submitButton="Send away!"
                    theme="positive"
                    icon="email"
                    @submit="send(test=false)"
                >
                    <k-text>
                        {{ $t('scardoso.newsletter.t.confirmSendNewsletter') }}
                    </k-text>
                </k-dialog>
                </template>
            </k-bar>
        <k-dialog
            ref="dialog_success"
            submitButton="Cool!"
            theme="positive"
            icon="check"
        >
            <k-text>
                Your mail was sent! :~)
            </k-text>
            <template slot="footer">
                <k-bar>
                    <template slot="center">
                        <k-button icon="check" theme="positive" @click="this.close()">Cool!</k-button>
                    </template>
                </k-bar>
            </template>
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
        slug: String,
    },
    methods: {
        send(test) {
            let dialog = (test) ? this.$refs.dialog_test : this.$refs.dialog_send;
            let isTest = (test) ? '/0' : '/1';
            let url = 'newsletter/send/' + encodeURI(this.pageURI) + isTest;
            this.$api.get(url)
            .then(response => {
                dialog.close();
                this.$refs.dialog_success.open();
                dialog.success(response.message);
                window.setTimeout(() => location.reload(), 2000);
            })
            .catch(error => {
                dialog.error(error.message);
            })
        },
    }
}
</script>

<style scoped>
.k-dialog-footer .k-button {
    padding: .75rem 1rem;
}
.k-newsletter-bar .k-button {
    margin-right: 20px;
}
</style>