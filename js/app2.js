function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

const app = Vue.createApp({
    data() {
        return {
            stockData: {},
            loading: false,   // ローディング中フラグ
            error: null       // エラーメッセージ
        };
    },
    mounted() {
        this.getStockPrice();
    },
    methods: {
        async getStockPrice() {
            const apiUrl2 = "https://test.jitsuda.com:5000";
            this.loading = true;  // ローディング開始
            this.error = null;    // エラーメッセージをリセット
            try {
                await sleep(1500); // フェッチ前に1.5秒待機
                const response = await fetch(apiUrl2);
                if (!response.ok) {
                    throw new Error('ネットワークエラー');
                }
                this.stockData = await response.json();
            } catch (error) {
                this.error = `エラーが発生しました: ${error.message}`;
            } finally {
                this.loading = false;  // フェッチ完了後、ローディングを終了
            }
        }
    },
    template: `
        <div>
            <button id="update-button" @click="getStockPrice"><img src="img/update.png"></button>
            <p v-if="loading">読み込み中...
                <div class="three-dot-spinner">
                    <div class="bounce1"></div>
                    <div class="bounce2"></div>
                    <div class="bounce3"></div>
                </div>
            </p>
            <p v-if="error">{{ error }}</p>
            <div class="fadeIn" v-if="!loading && stockData.symbol">
                <p><strong>銘柄コード:</strong> {{ stockData.symbol }}</p>
                <p><strong>現在価格:</strong> {{ stockData.current }}</p>
                <p><strong>始値:</strong> {{ stockData.open }}</p>
                <p><strong>高値:</strong> {{ stockData.high }}</p>
                <p><strong>安値:</strong> {{ stockData.low }}</p>
                <p><strong>前日終値:</strong> {{ stockData.close }}</p>
                <p><strong>タイムスタンプ:</strong> {{ stockData.timestamp }}</p>
            </div>
        </div>
    `
});
app.mount('#app');

const youkoso = Vue.createApp({
    data() {
        return {
            welcomeMessage: 'ようこそ、実田テクノロジーへ',
            message: 'Welcome Jitsuda Technology !'
        };
    },
    methods: {
        reverseMessages() {
            this.welcomeMessage = this.welcomeMessage.split('').reverse().join('');
            this.message = this.message.split('').reverse().join('');
        }
    }
});
youkoso.mount('#youkoso-app');
