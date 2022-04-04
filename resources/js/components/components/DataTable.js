import React from "react";
import { withRouter } from 'react-router-dom';
import ToolkitProvider from 'react-bootstrap-table2-toolkit';
import BootstrapTable from "react-bootstrap-table-next";
import paginationFactory from "react-bootstrap-table2-paginator";

import { Card, CardBody, CardHeader, Row, Col, Form, Input, UncontrolledDropdown, DropdownToggle, DropdownMenu, DropdownItem } from "reactstrap";
import { MoreHorizontal } from "react-feather";

class DataTable extends React.Component {
  constructor(props) {
    super(props)

    this.state = {
      // table: this.props.table,
      data: this.props.data,
      page: this.props.page, 
      sizePerPage: this.props.sizePerPage, 
      totalSize: this.props.totalSize,
      columns: this.props.columns,
      filters: this.props.filters !== undefined ? this.props.filters : null,
      pagination: this.props.pagination ? this.props.pagination : false,
      selectedRow: null
    }

    this.filterHandler = this.filterHandler.bind(this)
    this.getFilters = this.getFilters.bind(this)
    this.rowClasses = this.rowClasses.bind(this)
  }

  filterHandler(e) {
    this.setState({ 
      filters: {
        ...this.state.filters,
        [e.target.name]: e.target.value
      }
    });
  }

  getFilters(e) {
    this.props.handleFilter(this.state.filters)
    
    e.preventDefault()
  }

  rowEvents = {
    onClick: (e, row, rowIndex) => {
      this.setState({ selectedRow: rowIndex })

      if (this.props.action === 'link') {
        const getHash = window.location.hash.split('/')
        let parentPath = ''

        if (!this.props.parent && getHash.length > 3) {
          parentPath = `${ getHash[1] }/${ getHash[2] }/`
        }

        this.props.history.push(`/${ parentPath }${ this.props.table.replace(/\s+/g, '-').toLowerCase() }/${ row.id }/details`)
      } else if (this.props.action === 'edit') {
        this.props.toggleForm(e, row, rowIndex)
      }
    }
  }

  rowClasses = (row, rowIndex) => {
    let rowClass = ''
    if (rowIndex === this.state.selectedRow) {
      rowClass = 'selected-row'
    } else {
      rowClass = ''
    }

    return rowClass;
  }

  componentWillReceiveProps() {
    setTimeout(() => {
      this.setState({
        data: this.props.data,
        page: this.props.page, 
        sizePerPage: this.props.sizePerPage, 
        totalSize: this.props.totalSize,
        filters: this.props.filters,
        pagination: this.props.pagination
      })
    }, 1)
  }
 
  render() {
    const { filters } = this.state,
          { action } = this.props;
    return (
      <React.Fragment>
        {/* <h1 className="h3 mb-3">{ this.state.table }</h1> */}

        <Card>
        <ToolkitProvider
              keyField="id"
              data={ this.state.data }
              columns={ this.state.columns }
            >
              {
                props => (
                  <React.Fragment>
                    { this.props.toggleForm || filters ?
                      <CardHeader>
                        <Row className="align-items-center">
                          { filters !== null ?
                            <Col xs="12" sm="6" md="3" lg="2">
                              <Form className="d-flex" onSubmit={ this.getFilters }>
                                {
                                  Object.keys(filters).map((item, index) => 
                                    <Input 
                                      className={ `form-control form-control-md ${ index > 0 ? 'ml-1' : '' }` }
                                      type="text" 
                                      name={ item } 
                                      value={ filters[item] }
                                      placeholder="Search"
                                      onChange={ this.filterHandler }
                                      key={ index }
                                    />
                                  )
                                }
                                {/* <Button className="d-flex align-items-center ml-1" type="submit" color="secondary" size="md">
                                  <SearchIcon size="18" />
                                </Button> */}
                              </Form>
                            </Col> : null
                          }
                          { this.props.toggleForm ?
                            <Col className="d-flex" xs="12" md="auto" className="ml-auto">
                              <UncontrolledDropdown className="ml-auto">
                                <DropdownToggle nav className="px-3 py-1">
                                  <MoreHorizontal size={ 18 } />
                                </DropdownToggle>
              
                                <DropdownMenu right={ true }>
                                  <DropdownItem className="py-2" onClick={ this.props.toggleForm }>
                                    Add New
                                  </DropdownItem>
                                </DropdownMenu>
                              </UncontrolledDropdown>
                            </Col> : null
                          }
                        </Row>
                      </CardHeader> : null
                    }
                    <CardBody className={ `action-${ action }` }>
                      <BootstrapTable
                        {...props.baseProps}
                        bootstrap4
                        hover
                        bordered={ false }
                        remote={ { pagination: true } }
                        pagination={ this.state.pagination ?
                          paginationFactory({
                            page: this.state.page, 
                            sizePerPage: this.state.sizePerPage, 
                            totalSize: this.state.totalSize,
                            sizePerPageList: [10, 25, 50]
                          }) : null
                        }
                        onTableChange={ this.props.onTableChange }
                        rowEvents={ this.rowEvents }
                        rowClasses={ this.rowClasses }
                      />
                      </CardBody>
                  </React.Fragment>
                )
              }
            </ToolkitProvider>       
        </Card>
      </React.Fragment>
    )
  }
}

export default withRouter(DataTable);
