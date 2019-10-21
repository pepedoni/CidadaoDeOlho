<template>
  <div>
    <v-toolbar flat color="white">
      <v-toolbar-title>Redes Sociais Mais Utilizadas</v-toolbar-title>
      <v-spacer></v-spacer>
      
      <v-btn color="info" dark class="mb-2" @click="popularBaseDados">Popular Base de Dados</v-btn>
      <v-btn color="info" dark class="mb-2" @click="getDataFromApi">Recarregar</v-btn>
    </v-toolbar>
  
    <v-dialog
      v-model="loading"
      hide-overlay
      persistent
      width="300"
    >
      <v-card
        color="primary"
        dark
      >
        <v-card-text>
          Carregando ...
          <v-progress-linear
            indeterminate
            color="white"
          ></v-progress-linear>
        </v-card-text>
      </v-card>
    </v-dialog>

    <v-data-table
        :headers="headers"
        :items="redesSociais"
        :loading="true"
        :pagination.sync="pagination"
        :total-items="totalRedesSociais"
        class="elevation-1"
      >
        <v-progress-linear v-slot:progress color="blue" indeterminate></v-progress-linear>
        <template v-slot:items="props">
          <td >{{ props.item.NOME }}</td>
          <td class="text-xs-right">{{ props.item.QT_DEPUTADOS }}</td>
        </template>
    </v-data-table>
    <div class="text-xs-center footerTable">
      <v-btn @click="$router.push('/')" text dark>Voltar a Pagina Inicial</v-btn>
    </div>
    <v-snackbar
      v-model="snackbar"
      :color="snackColor"
      :timeout="2000"
    >
      {{ snackText }}
    </v-snackbar>
  </div>
</template>

<script>

export default {

  data() {
    return {
      redesSociais: [],
      headers: [
        {
          text: "Nome da Rede Social",
          align: "left",
          value: "NOME",
          sortable: false
        },
        { text: "Quantidade de Deputados", sortable: false,  value: "QT_DEPUTADOS", align: 'right' },
      ],
      endPoint: "http://127.0.0.1:8000/api/",
      loading: true,
      pagination: {
        pagination: 1,
        rowsPerPage: 5,
        totalRedesSociais: 1
      },
      snackbar: false,
      snackColor: "green",
      snackText: "Consulta Realizada com Sucesso",
      totalRedesSociais: 10
    };
  },
  created() {
    // this.getDataFromApi();
  },

  computed: {
    pages() {
      if (
        this.pagination.rowsPerPage == null ||
        this.pagination.totalDeputados == null
      )
        return 0;
      return Math.ceil(
        this.pagination.totalDeputados / this.pagination.rowsPerPage
      );
    }
  },

  watch: {
    pagination: {
      handler() {
        this.getDataFromApi();
      },
      deep: true
    }
  },

  methods: {

    getDeputadosOrdenadosPorVerba() {
        this.loading = true;
        let page = '';
        if(this.pagination.page) {
          page = '/?page=' + this.pagination.page;
        }
        return this.axios.get(this.endPoint + 'redes_sociais_mais_utilizadas' + page).then(response => {
          this.loading = false;
          this.snackText = "Consulta Realizada com Sucesso";
          this.snackColor = "green";
          this.snackbar = true;
          return response;
        }).catch(error => {
          this.loading = false;
          this.snackText = "Ocorreu um erro ao comunicar com o servidor";
          this.snackColor = "red";
          this.snackbar = true;
        });
    },

    getDataFromApi() {
       this.getDeputadosOrdenadosPorVerba().then(response => {
        this.pagination.totalDeputados = response.data.total;
        this.totalDeputados = response.data.total;
        this.pagination.rowsPerPage = response.data.per_page;
        
        if(!Array.isArray(response.data.data)) {
          this.redesSociais = Object.values(response.data.data);
        }
        else {
          this.redesSociais = response.data.data;
        }
      });
    },

    popularBaseDados() {
      this.loading = true;
      this.axios.get(this.endPoint + 'preencherDados').then(response => {
        this.loading = false;
        this.snackText = "Base de Dados Atualizada com Sucesso.";
        this.snackColor = "green";
        this.snackbar = true;
        return response;
      }).catch(error => {
          this.loading = false;
          this.snackText = "Ocorreu um erro ao comunicar com o servidor";
          this.snackColor = "red";
          this.snackbar = true;
      });
    }
  }
};
</script>