import React from "react";
import { connect } from "react-redux";
import { NavLink, withRouter } from "react-router-dom";

import { Badge, Collapse, UncontrolledDropdown, DropdownToggle, DropdownMenu, DropdownItem } from "reactstrap";
import PerfectScrollbar from "react-perfect-scrollbar";

import { Box } from "react-feather";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faCircle } from "@fortawesome/free-solid-svg-icons";

import routes from "../routes/index";
import avatar from "../assets/img/avatars/avatar.jpg";

import { GetMyTenants, SwitchTenant } from '../controllers/tenants';

const SidebarCategory = withRouter(
    ({ name, badgeColor, badgeText, icon: Icon, isOpen, children, onClick, location, to }) => {
        const getSidebarItemClass = path => {
            return location.pathname.indexOf(path) !== -1 ||
                (location.pathname === "/" && path === "/dashboard")
                ? "active"
                : "";
        };

        return (
            <li className={"sidebar-item " + getSidebarItemClass(to)}>
                <span
                    data-toggle="collapse"
                    className={"sidebar-link " + (!isOpen ? "collapsed" : "")}
                    onClick={onClick}
                    aria-expanded={isOpen ? "true" : "false"}
                >
                    <Icon size={18} className="align-middle mr-3" />
                    <span className="align-middle">{name}</span>
                    {badgeColor && badgeText ? (
                        <Badge color={badgeColor} size={18} className="sidebar-badge">
                            {badgeText}
                        </Badge>
                    ) : null}
                </span>
                <Collapse isOpen={isOpen}>
                    <ul id="item" className={"sidebar-dropdown list-unstyled"}>
                        {children}
                    </ul>
                </Collapse>
            </li>
        );
    }
);

const SidebarItem = withRouter(
    ({ name, badgeColor, badgeText, icon: Icon, location, to }) => {
        const getSidebarItemClass = path => {
            const basePath = `/${ location.pathname.split('/')[1] }`
            return basePath === path ? "active" : "";
        };

        return (
            <li className={"sidebar-item " + getSidebarItemClass(to)}>
                <NavLink to={to} className="sidebar-link" activeClassName="active">
                    {Icon ? <Icon size={18} className="align-middle mr-3" /> : null}
                    {name}
                    {badgeColor && badgeText ? (
                        <Badge color={badgeColor} size={18} className="sidebar-badge">
                            {badgeText}
                        </Badge>
                    ) : null}
                </NavLink>
            </li>
        );
    }
);

class Sidebar extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            tenant: '',
            tenants: null
        };
    }

    toggle = index => {
        this.setState(state => ({
            [index]: !state[index]
        }));
    };

    changeTenant(tenant) {
        this.setState({ tenant: tenant.name })
        this.setTenant(tenant.id)
    }

    setTenant(id) {
        SwitchTenant(id).then(res => {
            sessionStorage.setItem('tenant_id', res.data.data.id)

            const locationArr = this.props.location.pathname.split('/')

            if (locationArr.length > 2) {
                window.location.href = `/#/${ this.props.location.pathname.split('/')[1] }`
            }
        });
    }

    componentDidMount() {
        const params = {
            page: 0,
            offset: 0
        };

        (async () => {
            await GetMyTenants(params)
                .then(res => {
                    const tenants = res.data.data
                    
                    this.setState({ tenants })

                    if (sessionStorage.getItem('tenant_id')) {
                        const tenantId = sessionStorage.getItem('tenant_id'),
                              tenant = tenants.find(i => parseInt(i.id) === parseInt(tenantId));

                        this.setState({ tenant: tenant.name })
                        this.setTenant(tenantId)
                    } else {
                        this.setState({ tenant: 'F2x Operator' })
                    }
                })
                .catch(err => {
                    console.log(err)
                })
        })().catch(err => {
            console.log(err)
        })

        /* Open collapse element that matches current url */
        const pathName = this.props.location.pathname;

        routes.forEach((route, index) => {
            const isActive = pathName.indexOf(route.path) === 0;
            const isOpen = route.open;
            const isHome = route.containsHome && pathName === "/" ? true : false;

            this.setState(() => ({
                [index]: isActive || isOpen || isHome
            }));
        });
    }

    render() {
        const { sidebar, layout } = this.props;

        return (
            <nav
                className={
                    "sidebar" +
                    (!sidebar.isOpen ? " toggled" : "") +
                    (sidebar.isSticky ? " sidebar-sticky" : "")
                }
            >
                <div className="sidebar-content">
                    <PerfectScrollbar>
                        <div className="sidebar-brand">
                            <UncontrolledDropdown>
                                <DropdownToggle nav className="d-flex px-0">
                                    <Box className="align-middle text-primary mr-2" size={24} />{" "}
                                    {this.state.tenant}
                                </DropdownToggle>

                                {this.state.tenants && this.state.tenants.length > 1 ?
                                    <DropdownMenu className="dropdown-menu-md">
                                        {
                                            Object.keys(this.state.tenants).map((item, index) =>
                                                <React.Fragment key={index}>
                                                    {
                                                        this.state.tenants[item].name !== this.state.tenant ?
                                                            <DropdownItem className="py-2" onClick={() => this.changeTenant(this.state.tenants[item])}>
                                                                {this.state.tenants[item].name}
                                                            </DropdownItem> : null
                                                    }
                                                </React.Fragment>
                                            )
                                        }
                                    </DropdownMenu> : null
                                }
                            </UncontrolledDropdown>
                        </div>

                        <ul className="sidebar-nav">
                            {routes.map((category, index) => {
                                return (
                                    <React.Fragment key={index}>
                                        {category.header ? (
                                            <li className="sidebar-header">{category.header}</li>
                                        ) : null}

                                        {category.children ? (
                                            <SidebarCategory
                                                name={category.name}
                                                badgeColor={category.badgeColor}
                                                badgeText={category.badgeText}
                                                icon={category.icon}
                                                to={category.path}
                                                isOpen={this.state[index]}
                                                onClick={() => this.toggle(index)}
                                            >
                                                {category.children.map((route, index) => (
                                                    <SidebarItem
                                                        key={index}
                                                        name={route.name}
                                                        to={route.path}
                                                        badgeColor={route.badgeColor}
                                                        badgeText={route.badgeText}
                                                    />
                                                ))}
                                            </SidebarCategory>
                                        ) : (
                                                <SidebarItem
                                                    name={category.name}
                                                    to={category.path}
                                                    icon={category.icon}
                                                    badgeColor={category.badgeColor}
                                                    badgeText={category.badgeText}
                                                />
                                            )}
                                    </React.Fragment>
                                );
                            })}
                        </ul>

                        {/* {!layout.isBoxed && !sidebar.isSticky ? (
              <div className="sidebar-bottom d-none d-lg-block">
                <div className="media">
                  <img
                    className="rounded-circle mr-3"
                    src={avatar}
                    alt="Chris Wood"
                    width="40"
                    height="40"
                  />
                  <div className="media-body">
                    <h5 className="mb-1">Chris Wood</h5>
                    <div>
                      <FontAwesomeIcon
                        icon={faCircle}
                        className="text-success"
                      />{" "}
                      Online
                    </div>
                  </div>
                </div>
              </div>
            ) : null} */}
                    </PerfectScrollbar>
                </div>
            </nav>
        );
    }
}

export default withRouter(
    connect(store => ({
        sidebar: store.sidebar,
        layout: store.layout
    }))(Sidebar)
);
