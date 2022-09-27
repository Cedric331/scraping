<template>
    <Head title="Welcome" />


    <!-- component -->
    <div>
        <div @click="this.$refs.inputSearch.focus()" class="w-full min-h-screen shadow-2xl subpixel-antialiased rounded h-64 bg-black border-black mx-auto">
            <div class="flex items-center h-6 rounded-t bg-gray-100 border-b border-gray-500 text-center text-black" id="headerTerminal">
                <div class="mx-auto pr-16" id="terminaltitle">
                    <p class="text-center text-sm">Terminal</p>
                </div>

            </div>
            <div class="pl-1 pt-1 h-auto text-green-200 font-mono text-xs bg-black" id="console">
                <p class="pb-1">Ce site permet de faire du Web scraping faire le site "https://www.larousse.fr". Cela effectue des recherches dans le dictionnaire.</p>
                <p class="pb-1">Site web créé par : LIMA Cédric - Développeur Full Stack Laravel / VueJS</p>
                <br/>
                <p class="border-2 border-dashed w-max p-2">Veuillez appuyer sur <br/>
                    "Entrer" pour lancer la recherche. <br/>
                    "Echap" pour nettoyer. <br/>
                    "Up" pour remonter dans les recherches. <br/>
                    "Down" pour descendre.
                </p>
                <br/>
                <p class="pb-1">Last login: {{ this.date }} on User{{ this.numberUser }}</p>
                <br/>
                <p v-html="element"></p>
                <p ref="valueSearch">Votre Recherche : <input v-model="search"  @keyup.down="searchDownCache()" @keyup.up="searchUpCache()" @keyup.esc="refreshElement()" @keyup.enter="spider()" ref="inputSearch" class="m-0 p-0 text-xs bg-black border-transparent focus:border-transparent focus:ring-0" type="text" autofocus></p>
                <p v-if="loading" class="pb-1 w-max">
                    Recherche en cours de traitement...
                </p>
            </div>
        </div>
    </div>
</template>

<script>
import { Head, Link } from '@inertiajs/inertia-vue3';

export default {
    props: {
        date: String,
        numberUser: Number,
        canLogin: Boolean,
        canRegister: Boolean,
    },
    data () {
        return {
            loading: false,
            searchs: [],
            up: 0,
            search: '',
            element: ''
        }
    },
    methods: {
        spider() {
            this.loading = true

            axios.post('spider', {
                input: this.search
            })
                .then(res => {
                    this.element += "<p class=\"pb-1\">Votre Recherche : " + this.search + "</p>"
                    if (res.status === 200) {
                        this.element += '----------------------------------------'
                        this.element += '</br>'
                        this.element += 'Titre : ' + res.data.title
                        this.element += '</br>'
                        let defs
                        res.data.defs.forEach(value => {
                            if (value !== 'undefined') {
                                defs += value + '</br>'
                            }
                        })
                        defs = defs.replace('undefined','');
                        this.element += 'Définitions : </br>' + defs
                        this.element += '----------------------------------------'
                        this.storeCache()
                    } else {
                        this.element += "<p class=\"pb-1\">Aucun résultat pour cette recherche...</p>"
                    }
                })
                .catch(err => {
                    this.element += "<p class=\"pb-1\">Vous devez renseigner une recherche valide</p>"
                    console.log(err)
                })
                .finally(() => {
                    this.loading = false
                    this.search = ''
                })
        },
        refreshElement () {
            this.element = ''
            this.search = ''
            this.$refs.inputSearch.focus()
            axios.delete('cache')
                .then(() => {
                    //
                })
                .catch(err => {
                    console.log(err)
                })
        },
        storeCache () {
            axios.post('cache', {
                search: this.search,
                data: this.element,
            })
            .then(() => {
                //
            })
            .catch(err => {
                console.log(err)
            })
        },
        getCache () {
            axios.get('cache')
                .then((res) => {
                    if (res.data.elements) {
                        this.element = res.data.elements
                    }
                    if (res.data.search) {
                        this.searchs = res.data.search
                    }
                })
                .catch(err => {
                    console.log(err)
                })
        },
        searchUpCache () {
            if (this.searchs && this.searchs[this.up]) {
                if (this.search === this.searchs[this.up] && this.searchs[this.up + 1]) {
                    this.up++
                }
                 if (this.searchs[this.up]) {
                     this.search = this.searchs[this.up]
                }
            }
        },
        searchDownCache () {
            if (this.searchs && this.searchs[this.up]) {
                if (this.search === this.searchs[this.up] && this.searchs[this.up - 1]) {
                    this.up--
                    this.search = this.searchs[this.up]
                } else {
                    this.search = ''
                }
            }
        }
    },
    mounted () {
        this.getCache()
    }
}
</script>
