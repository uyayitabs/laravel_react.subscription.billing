import React from "react";
import { Link } from "react-router-dom";

import { Container, UncontrolledDropdown, DropdownToggle, DropdownMenu, DropdownItem } from "reactstrap";
import { Edit, ExternalLink, MoreHorizontal } from "react-feather";

import Loader from './Loader';
import DataTable from './DataTable';

class ParentTable extends React.Component {
  isMounted = false;

  constructor(props) {
    super(props)

    this.state = {
      data: [],
      selectedData: null,
      selectedDataRow: null,
      isOpen: false,
      loading: true,
      page: 1,
      sizePerPage: 15,
      totalSize: 0,
      filters: { keyword: '' },
      columns: this.props.columns,
      currentTenant: sessionStorage.getItem('tenant_id') ? sessionStorage.getItem('tenant_id') : null
    }

    this.toggleModal = this.toggleModal.bind(this)
    this.handleFilter = this.handleFilter.bind(this)
    this.checkTenantChange = this.checkTenantChange.bind(this)
    this.updateDataTable = this.updateDataTable.bind(this)
    this.getData = this.getData.bind(this)
  }

  toggleModal(e, row, index) {
    if (!isNaN(index)) {
      this.handleData(e, row, index)
    } else {
      if (this.state.isOpen) {
        this.setState({ selectedData: null })
      }

      this.setState({ isOpen: !this.state.isOpen });
    }
  }

  actionsFormatter = (cell, row, rowIndex, formatExtraData) => {
    return (
      <React.Fragment>
        { this.props.action === 'edit' ?
          <UncontrolledDropdown className="ml-auto">
            <DropdownToggle nav className="px-3 py-2 dropdown-table-actions" onClick={(e) => { this.stopBubble(e) }}>
                <MoreHorizontal size={ 18 } />
            </DropdownToggle>

            <DropdownMenu right={ true }>
                <DropdownItem className="py-2" onClick={(e) => { this.handleData(e, row, rowIndex) }}>
                    Edit
                </DropdownItem>
            </DropdownMenu>
          </UncontrolledDropdown> :
          <Link to={ `/${ this.props.table.replace(/\s+/g, '-').toLowerCase() }/${ row.id }/details` }><ExternalLink /></Link>
        }
      </React.Fragment>
    );
  }

  stopBubble(e) {
    e.stopPropagation()
  }

  handleData(e, selectedData, selectedDataRow) {
    e.stopPropagation()
    this.setState({ selectedData, selectedDataRow })
    this.toggleModal()
  }

  getData() {
    (async () => {
      const filter = this.state.filters;

      let params = {
        page: this.state.page,
        offset: this.state.sizePerPage,
        sort: this.state.sortField,
        direction: this.state.sortOrder
      }

      if (this.props.include) {
        params.include = this.props.include
      }

      Object.keys(filter).forEach(item => {
        if (filter[item] !== '') {
          const getString = `filter[${item}]`,
                  getValue = filter[item];
          
          params[getString] = getValue
        }
      })

      const requestData = this.props.data,
            id = this.props.id ? this.props.id : '',
            id2 = this.props.id2 ? this.props.id2 : '';
            
      await requestData(params, id, id2)
          .then(res => {
            const data = res.data.data;

            this.setState({
              data,
              totalSize: res.data.total,
              loading: false
            });
          })
          .catch(err => {
            console.log(err)
          });
    })()
    .catch(err => {
      console.log(err)
    })
  }

  handleTableChange = (type, { page, sizePerPage, sortField, sortOrder }) => {
    console.log(type, page, sizePerPage)
    if (type === 'pagination') {
      this.setState({
        page,
        sizePerPage,
        loading: true
      })
    }

    if (type === 'sort') { 
      this.setState({
        sortOrder: sortOrder,
        sortField: sortField,
        page,
        sizePerPage,
        loading: true
      })
    }

    setTimeout(() => {
      this.getData()
    }, 1)
  }

  handleFilter(filters) {
    this.setState({ filters })

    setTimeout(() => {
      this.getData()
    }, 1)
  }

  checkTenantChange() {
    if ((this.state.currentTenant === sessionStorage.getItem('tenant_id')) || (parseInt(this.state.currentTenant) === parseInt(sessionStorage.getItem('tenant_id')))) {
      setTimeout(this.checkTenantChange, 100)
    } else {
      this.setState({ 
        currentTenant: sessionStorage.getItem('tenant_id'),
        loading: true
      })
            
      setTimeout(this.updateDataTable, 100)
    }
  }

  updateDataTable() {
    if (this.isMounted) {
      this.getData()
      this.checkTenantChange()
    }
  }

  componentWillMount() {
    if (this.props.action === 'edit') {
      this.setState({
        columns: [
          ...this.state.columns,
          {
            dataField: "actions",
            text: "",
            formatter: this.actionsFormatter
          }
        ]
      })
    }
  }

  componentDidMount() {
    this.isMounted = true;
    this.updateDataTable()
  }

  componentWillUnmount() {
    this.isMounted = false;
  }
 
  render() {
    const { data, selectedData, selectedDataRow, isOpen, loading, page, sizePerPage, totalSize, filters, columns } = this.state,
          { id, id2, table, search, pagination, action, parent } = this.props,
          Form = this.props.form;

    return (
      <Container fluid className="p-0">
        { loading ? <Loader /> : null }
        { data ?
          <DataTable
            table={ table ? table : undefined }
            data={ data }
            page={ page }
            sizePerPage={ sizePerPage }
            totalSize={ totalSize }
            onTableChange={ this.handleTableChange }
            columns={ columns }
            filters={ search === undefined ? filters : null }
            handleFilter={ this.handleFilter }
            pagination={ pagination === undefined && totalSize > sizePerPage ? true : false }
            toggleForm={ Form ? this.toggleModal : null }
            action={ action }
            parent={ parent ? true : false }
          /> : null
        }

        { Form && isOpen ?
          <Form
            id={ id }
            id2={ id2 }
            show={ isOpen }
            hide={ this.toggleModal }
            selectedData={ selectedData }
            selectedDataRow={ selectedDataRow }
            update={ this.getData }
          /> : null
        }
      </Container>
    )
  }
}

export default ParentTable;
