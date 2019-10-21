<template>
  <div>
    <v-toolbar flat color="white">
      <v-toolbar-title>Deputados Ordenados por Verba Indenizatoria</v-toolbar-title>
      <v-spacer></v-spacer>

      <v-btn color="info" dark class="mb-2" @click="openMonthDialog"> {{ labelMes }}</v-btn>
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

    <v-dialog
      v-model="monthDialog"
      width="290"
      overlay-opacity="100"
    >
      <v-date-picker
        type="month"
        no-title
        @change="trocarMes"
        locale="pt-BR"
        prev-icon=false
        next-icon=false
      >
        <v-spacer></v-spacer>
        <v-btn text color="info" dark class="mb-2" @click="monthDialog = false">Cancelar</v-btn>
      </v-date-picker>
    </v-dialog>

    <v-data-table
        :headers="headers"
        :items="deputadosVerba"
        :loading="true"
        :pagination.sync="pagination"
        :total-items="totalDeputados"
        class="elevation-1"
      >
        <v-progress-linear v-slot:progress color="blue" indeterminate></v-progress-linear>
        <template v-slot:items="props">
          <td >{{ props.item.NOME }}</td>
          <td class="text-xs-right">{{ props.item.SITUACAO }}</td>
          <td class="text-xs-right">{{ props.item.VALOR_DESPESA }}</td>
          <td class="text-xs-right">{{ props.item.VALOR_REEMBOLSO }}</td>
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
      deputadosVerba: [],
      meses: [
        'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
        'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'
      ],
      headers: [
        {
          text: "Nome do Deputado",
          align: "left",
          value: "NOME",
          sortable: false
        },
        { text: "Situação", sortable: false, value: "SITUACAO", align: 'right' },
        { text: "Valor Gasto", sortable: false, value: "VALOR_DESPESA", align: 'right' },
        { text: "Valor Reembolsado", sortable: false, value: "VALOR_REEMBOLSO", align: 'right' },
      ],
      endPoint: "http://127.0.0.1:8000/api/",
      snackbar: false,
      snackColor: "green",
      snackText: "Consulta Realizada com Sucesso",
      loading: true,
      monthDialog: false,
      pagination: {
        pagination: 1,
        rowsPerPage: 10,
        totalDeputados: 1
      },
      mask: '02/2019',
      labelMes: 'Janeiro',
      mes: 1,
      totalDeputados: 10
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
        return this.axios.get(this.endPoint + 'deputados_por_verba/' + this.mes + page).then(response => {
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
          this.deputadosVerba = Object.values(response.data.data);
        }
        else {
          this.deputadosVerba = response.data.data;
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
    },

    openMonthDialog() {
      this.monthDialog = true;
    },

    trocarMes(data) {
      this.mes = parseInt(data.substr(5, 2));
      this.labelMes = this.meses[this.mes - 1];
      this.monthDialog = false;
      this.getDataFromApi();
    },
  }
};
</script>